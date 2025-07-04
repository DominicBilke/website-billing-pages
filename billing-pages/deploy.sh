#!/bin/bash

# Billing Pages Deployment Script for Ubuntu 24.04 with Plesk
# This script automates the deployment process

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="billing-pages.com"
DB_PREFIX="billing_pages"
DB_USER="billing_user"
DB_PASSWORD=""
WEB_ROOT="/var/www/vhosts/${DOMAIN}/httpdocs"
BACKUP_DIR="/var/www/vhosts/${DOMAIN}/backups"

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if running as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "This script must be run as root"
        exit 1
    fi
}

# Function to check if Plesk is installed
check_plesk() {
    if ! command -v plesk &> /dev/null; then
        print_error "Plesk is not installed. Please install Plesk first."
        exit 1
    fi
    print_success "Plesk is installed"
}

# Function to generate secure password
generate_password() {
    if [[ -z "$DB_PASSWORD" ]]; then
        DB_PASSWORD=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
        print_status "Generated database password: $DB_PASSWORD"
    fi
}

# Function to create domain in Plesk
create_domain() {
    print_status "Creating domain in Plesk..."
    
    if ! plesk bin domain --info "${DOMAIN}" &> /dev/null; then
        plesk bin domain --create "${DOMAIN}" -owner admin -ip 0.0.0.0
        print_success "Domain ${DOMAIN} created"
    else
        print_warning "Domain ${DOMAIN} already exists"
    fi
}

# Function to configure PHP
configure_php() {
    print_status "Configuring PHP..."
    
    # Enable PHP 8.1
    plesk bin php_handler --add -displayname "PHP 8.1" -type fpm -phpini /opt/plesk/php/8.1/etc/php.ini -clipath /opt/plesk/php/8.1/bin/php
    
    # Set PHP version for domain
    plesk bin domain --set-php-version "${DOMAIN}" -php-version 8.1
    
    # Configure PHP settings
    cat > /tmp/php.ini << EOF
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
max_input_vars = 3000
date.timezone = Europe/Berlin
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
EOF
    
    plesk bin domain --set-php-ini "${DOMAIN}" -php-ini /tmp/php.ini
    rm /tmp/php.ini
    
    print_success "PHP configured"
}

# Function to create databases
create_databases() {
    print_status "Creating databases..."
    
    # Main database
    mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_PREFIX}_main CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    # Module databases
    for module in companies tours work tasks money; do
        mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_PREFIX}_${module} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    done
    
    # Create database user
    mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
    mysql -e "GRANT ALL PRIVILEGES ON ${DB_PREFIX}_*.* TO '${DB_USER}'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    
    print_success "Databases created"
}

# Function to import database schemas
import_schemas() {
    print_status "Importing database schemas..."
    
    # Import main schema
    mysql "${DB_PREFIX}_main" < database/schema.sql
    
    # Import module schemas
    mysql "${DB_PREFIX}_companies" < database/companies_schema.sql
    mysql "${DB_PREFIX}_tours" < database/tours_schema.sql
    mysql "${DB_PREFIX}_work" < database/work_schema.sql
    mysql "${DB_PREFIX}_tasks" < database/tasks_schema.sql
    mysql "${DB_PREFIX}_money" < database/money_schema.sql
    
    print_success "Database schemas imported"
}

# Function to deploy application files
deploy_files() {
    print_status "Deploying application files..."
    
    # Create web root if it doesn't exist
    mkdir -p "${WEB_ROOT}"
    
    # Copy application files
    cp -r public/* "${WEB_ROOT}/"
    cp -r src "${WEB_ROOT}/"
    cp -r config "${WEB_ROOT}/"
    cp -r database "${WEB_ROOT}/"
    cp composer.json "${WEB_ROOT}/"
    cp composer.lock "${WEB_ROOT}/" 2>/dev/null || true
    
    # Create necessary directories
    mkdir -p "${WEB_ROOT}/logs"
    mkdir -p "${WEB_ROOT}/temp"
    mkdir -p "${WEB_ROOT}/uploads"
    mkdir -p "${WEB_ROOT}/vendor"
    
    # Set proper permissions
    chown -R psaserv:psaserv "${WEB_ROOT}"
    chmod -R 755 "${WEB_ROOT}"
    chmod -R 777 "${WEB_ROOT}/logs"
    chmod -R 777 "${WEB_ROOT}/temp"
    chmod -R 777 "${WEB_ROOT}/uploads"
    
    print_success "Application files deployed"
}

# Function to install Composer dependencies
install_dependencies() {
    print_status "Installing Composer dependencies..."
    
    cd "${WEB_ROOT}"
    
    # Install Composer if not available
    if ! command -v composer &> /dev/null; then
        print_status "Installing Composer..."
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer
        chmod +x /usr/local/bin/composer
    fi
    
    # Install dependencies
    composer install --no-dev --optimize-autoloader
    
    print_success "Dependencies installed"
}

# Function to configure environment
configure_environment() {
    print_status "Configuring environment..."
    
    # Create .env file
    cat > "${WEB_ROOT}/.env" << EOF
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${DOMAIN}

DB_HOST=localhost
DB_PORT=3306
DB_MAIN=${DB_PREFIX}_main
DB_COMPANIES=${DB_PREFIX}_companies
DB_TOURS=${DB_PREFIX}_tours
DB_WORK=${DB_PREFIX}_work
DB_TASKS=${DB_PREFIX}_tasks
DB_MONEY=${DB_PREFIX}_money
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASSWORD}

MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@${DOMAIN}
MAIL_FROM_NAME=Billing Pages

LOG_LEVEL=info
MAPS_API_KEY=
EOF
    
    # Set proper permissions for .env
    chown psaserv:psaserv "${WEB_ROOT}/.env"
    chmod 600 "${WEB_ROOT}/.env"
    
    print_success "Environment configured"
}

# Function to configure SSL
configure_ssl() {
    print_status "Configuring SSL certificate..."
    
    # Enable SSL for domain
    plesk bin domain --set-ssl-cert "${DOMAIN}" -certificate-file /dev/null -private-key-file /dev/null
    
    # Force HTTPS redirect
    cat > "${WEB_ROOT}/.htaccess" << EOF
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Route all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOF
    
    print_success "SSL configured"
}

# Function to create backup script
create_backup_script() {
    print_status "Creating backup script..."
    
    mkdir -p "${BACKUP_DIR}"
    
    cat > "${BACKUP_DIR}/backup.sh" << 'EOF'
#!/bin/bash

# Backup script for Billing Pages
DOMAIN="billing-pages.com"
BACKUP_DIR="/var/www/vhosts/${DOMAIN}/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "${BACKUP_DIR}/${DATE}"

# Backup files
tar -czf "${BACKUP_DIR}/${DATE}/files.tar.gz" -C /var/www/vhosts/${DOMAIN} httpdocs

# Backup databases
mysqldump billing_pages_main > "${BACKUP_DIR}/${DATE}/main.sql"
mysqldump billing_pages_companies > "${BACKUP_DIR}/${DATE}/companies.sql"
mysqldump billing_pages_tours > "${BACKUP_DIR}/${DATE}/tours.sql"
mysqldump billing_pages_work > "${BACKUP_DIR}/${DATE}/work.sql"
mysqldump billing_pages_tasks > "${BACKUP_DIR}/${DATE}/tasks.sql"
mysqldump billing_pages_money > "${BACKUP_DIR}/${DATE}/money.sql"

# Compress database backups
tar -czf "${BACKUP_DIR}/${DATE}/databases.tar.gz" -C "${BACKUP_DIR}/${DATE}" *.sql
rm "${BACKUP_DIR}/${DATE}"/*.sql

# Keep only last 7 days of backups
find "${BACKUP_DIR}" -type d -mtime +7 -exec rm -rf {} \;

echo "Backup completed: ${BACKUP_DIR}/${DATE}"
EOF
    
    chmod +x "${BACKUP_DIR}/backup.sh"
    chown psaserv:psaserv "${BACKUP_DIR}/backup.sh"
    
    # Add to crontab
    (crontab -l 2>/dev/null; echo "0 2 * * * ${BACKUP_DIR}/backup.sh") | crontab -
    
    print_success "Backup script created"
}

# Function to create deployment summary
create_summary() {
    print_status "Creating deployment summary..."
    
    cat > "${WEB_ROOT}/DEPLOYMENT_SUMMARY.txt" << EOF
Billing Pages Deployment Summary
================================

Domain: ${DOMAIN}
Deployment Date: $(date)
Web Root: ${WEB_ROOT}

Database Configuration:
- Main Database: ${DB_PREFIX}_main
- Companies Database: ${DB_PREFIX}_companies
- Tours Database: ${DB_PREFIX}_tours
- Work Database: ${DB_PREFIX}_work
- Tasks Database: ${DB_PREFIX}_tasks
- Money Database: ${DB_PREFIX}_money
- Database User: ${DB_USER}
- Database Password: ${DB_PASSWORD}

Important Files:
- Configuration: ${WEB_ROOT}/config/
- Environment: ${WEB_ROOT}/.env
- Logs: ${WEB_ROOT}/logs/
- Uploads: ${WEB_ROOT}/uploads/

Next Steps:
1. Update the .env file with your specific configuration
2. Set up email configuration
3. Configure your domain DNS
4. Test the application
5. Set up regular backups

Support:
- Check logs in ${WEB_ROOT}/logs/
- Review configuration in ${WEB_ROOT}/config/
- Backup location: ${BACKUP_DIR}

Security Notes:
- Change default passwords
- Configure firewall rules
- Set up SSL certificate
- Regular security updates
EOF
    
    print_success "Deployment summary created"
}

# Function to test deployment
test_deployment() {
    print_status "Testing deployment..."
    
    # Test database connection
    if mysql -u"${DB_USER}" -p"${DB_PASSWORD}" -e "USE ${DB_PREFIX}_main; SELECT 1;" &> /dev/null; then
        print_success "Database connection test passed"
    else
        print_error "Database connection test failed"
        return 1
    fi
    
    # Test web access
    if curl -s -o /dev/null -w "%{http_code}" "https://${DOMAIN}" | grep -q "200\|302"; then
        print_success "Web access test passed"
    else
        print_warning "Web access test failed (this might be normal if DNS is not configured yet)"
    fi
    
    print_success "Deployment tests completed"
}

# Main deployment function
main() {
    print_status "Starting Billing Pages deployment..."
    
    check_root
    check_plesk
    generate_password
    create_domain
    configure_php
    create_databases
    import_schemas
    deploy_files
    install_dependencies
    configure_environment
    configure_ssl
    create_backup_script
    create_summary
    test_deployment
    
    print_success "Deployment completed successfully!"
    print_status "Please review the deployment summary at: ${WEB_ROOT}/DEPLOYMENT_SUMMARY.txt"
    print_status "Next steps:"
    print_status "1. Configure your domain DNS to point to this server"
    print_status "2. Update the .env file with your specific settings"
    print_status "3. Test the application at https://${DOMAIN}"
    print_status "4. Set up regular backups"
}

# Run main function
main "$@" 