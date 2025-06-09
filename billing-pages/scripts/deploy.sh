#!/bin/bash

# Exit on error
set -e

# Configuration
APP_NAME="billing-pages"
APP_DIR="/var/www/$APP_NAME"
BACKUP_DIR="/var/backups/$APP_NAME"
DB_NAME="billing_pages"
DB_USER="your_db_user"
DB_PASS="your_db_password"
DOMAIN="your-domain.com"
ADMIN_EMAIL="admin@your-domain.com"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Log function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root"
fi

# Create backup before deployment
backup() {
    log "Creating backup..."
    DATE=$(date +%Y-%m-%d_%H-%M-%S)
    
    # Backup database
    mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz
    
    # Backup files
    if [ -d "$APP_DIR" ]; then
        tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz $APP_DIR
    fi
    
    log "Backup completed"
}

# Install system dependencies
install_dependencies() {
    log "Installing system dependencies..."
    
    # Update system
    apt update
    apt upgrade -y
    
    # Install required packages
    apt install -y apache2 php8.1 php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring \
        php8.1-xml php8.1-zip php8.1-intl php8.1-bcmath php8.1-gmp php8.1-ldap \
        php8.1-imap php8.1-soap php8.1-xmlrpc php8.1-common php8.1-cli php8.1-json \
        php8.1-readline php8.1-opcache mysql-server fail2ban logwatch
    
    # Install Composer if not exists
    if ! command -v composer &> /dev/null; then
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer
    fi
    
    # Install Node.js if not exists
    if ! command -v node &> /dev/null; then
        curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
        apt install -y nodejs
    fi
    
    log "System dependencies installed"
}

# Configure Apache
configure_apache() {
    log "Configuring Apache..."
    
    # Create Apache virtual host configuration
    cat > /etc/apache2/sites-available/$APP_NAME.conf << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAdmin $ADMIN_EMAIL
    DocumentRoot $APP_DIR

    <Directory $APP_DIR>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$APP_NAME-error.log
    CustomLog \${APACHE_LOG_DIR}/$APP_NAME-access.log combined
</VirtualHost>
EOF
    
    # Enable required Apache modules
    a2enmod rewrite
    a2enmod ssl
    a2enmod headers
    
    # Enable the site
    a2ensite $APP_NAME.conf
    
    # Disable default site
    a2dissite 000-default.conf
    
    # Restart Apache
    systemctl restart apache2
    
    log "Apache configured"
}

# Configure MySQL
configure_mysql() {
    log "Configuring MySQL..."
    
    # Create database and user
    mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
    mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    
    log "MySQL configured"
}

# Configure PHP
configure_php() {
    log "Configuring PHP..."
    
    # Update PHP configuration
    cat > /etc/php/8.1/apache2/conf.d/99-custom.ini << EOF
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
date.timezone = UTC
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
opcache.enable_cli = 1
EOF
    
    # Create PHP log directory
    mkdir -p /var/log/php
    chown www-data:www-data /var/log/php
    
    # Restart Apache
    systemctl restart apache2
    
    log "PHP configured"
}

# Configure security
configure_security() {
    log "Configuring security..."
    
    # Configure fail2ban
    cat > /etc/fail2ban/jail.local << EOF
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600
findtime = 600

[apache]
enabled = true
port = http,https
filter = apache-auth
logpath = /var/log/apache2/error.log
maxretry = 3
bantime = 3600
findtime = 600
EOF
    
    # Restart fail2ban
    systemctl restart fail2ban
    
    # Configure logwatch
    cat > /etc/logwatch/conf/logwatch.conf << EOF
MailFrom = $ADMIN_EMAIL
MailTo = $ADMIN_EMAIL
Detail = High
Range = Today
EOF
    
    log "Security configured"
}

# Deploy application
deploy_app() {
    log "Deploying application..."
    
    # Create application directory
    mkdir -p $APP_DIR
    
    # Set proper permissions
    chown -R www-data:www-data $APP_DIR
    chmod -R 755 $APP_DIR
    
    # Create required directories
    mkdir -p $APP_DIR/uploads
    mkdir -p $APP_DIR/logs
    mkdir -p $APP_DIR/temp
    
    # Set permissions for special directories
    chmod -R 775 $APP_DIR/uploads
    chmod -R 775 $APP_DIR/logs
    chmod -R 775 $APP_DIR/temp
    
    # Install Composer dependencies
    cd $APP_DIR
    composer install --no-dev --optimize-autoloader
    
    # Create .env file
    cat > $APP_DIR/.env << EOF
# Database Configuration
DB_HOST=localhost
DB_USER=$DB_USER
DB_PASS=$DB_PASS
DB_NAME=$DB_NAME

# Application Settings
SITE_NAME=Billing Pages
SITE_URL=https://$DOMAIN
ADMIN_EMAIL=$ADMIN_EMAIL

# Security Settings
APP_ENV=production
APP_DEBUG=false
APP_KEY=$(openssl rand -base64 32)

# Mail Settings
MAIL_HOST=smtp.$DOMAIN
MAIL_PORT=587
MAIL_USERNAME=noreply@$DOMAIN
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$DOMAIN
MAIL_FROM_NAME="Billing Pages"

# Session Settings
SESSION_LIFETIME=3600
SESSION_SECURE=true
SESSION_HTTP_ONLY=true

# File Upload Settings
UPLOAD_MAX_SIZE=10485760
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx,xls,xlsx
EOF
    
    # Set proper permissions for .env
    chown www-data:www-data $APP_DIR/.env
    chmod 600 $APP_DIR/.env
    
    log "Application deployed"
}

# Configure SSL
configure_ssl() {
    log "Configuring SSL..."
    
    # Install certbot
    apt install -y certbot python3-certbot-apache
    
    # Obtain SSL certificate
    certbot --apache -d $DOMAIN --non-interactive --agree-tos --email $ADMIN_EMAIL
    
    log "SSL configured"
}

# Main deployment process
main() {
    log "Starting deployment process..."
    
    # Create backup
    backup
    
    # Install dependencies
    install_dependencies
    
    # Configure services
    configure_apache
    configure_mysql
    configure_php
    configure_security
    
    # Deploy application
    deploy_app
    
    # Configure SSL
    configure_ssl
    
    log "Deployment completed successfully!"
}

# Run main function
main 