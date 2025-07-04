#!/bin/bash

# Billing Pages - Modern Web Application Deployment Script
# For Ubuntu 24.04 with Plesk
# Version: 2.0.0

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="billing-pages.com"
APP_NAME="billing-pages"
APP_DIR="/var/www/vhosts/$DOMAIN"
BACKUP_DIR="/var/www/backups"
LOG_FILE="/var/log/billing-pages-deploy.log"
NODE_VERSION="18"
PHP_VERSION="8.2"

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}" | tee -a "$LOG_FILE"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}" | tee -a "$LOG_FILE"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root. Please run as a regular user with sudo privileges."
fi

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to install package if not exists
install_package() {
    local package=$1
    if ! dpkg -l | grep -q "^ii  $package "; then
        log "Installing $package..."
        sudo apt-get install -y "$package"
    else
        info "$package is already installed"
    fi
}

# Function to create directory if not exists
create_directory() {
    local dir=$1
    if [[ ! -d "$dir" ]]; then
        log "Creating directory: $dir"
        sudo mkdir -p "$dir"
        sudo chown $USER:$USER "$dir"
    fi
}

# Function to backup existing installation
backup_existing() {
    if [[ -d "$APP_DIR" ]]; then
        log "Creating backup of existing installation..."
        local backup_name="${APP_NAME}_backup_$(date +%Y%m%d_%H%M%S)"
        local backup_path="$BACKUP_DIR/$backup_name"
        
        create_directory "$BACKUP_DIR"
        sudo cp -r "$APP_DIR" "$backup_path"
        log "Backup created at: $backup_path"
    fi
}

# Function to install Node.js
install_nodejs() {
    if ! command_exists node; then
        log "Installing Node.js $NODE_VERSION..."
        
        # Add NodeSource repository
        curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
        
        # Install Node.js
        sudo apt-get install -y nodejs
        
        # Verify installation
        node --version
        npm --version
        
        log "Node.js installation completed"
    else
        info "Node.js is already installed: $(node --version)"
    fi
}

# Function to install PHP and extensions
install_php() {
    log "Installing PHP $PHP_VERSION and required extensions..."
    
    # Add PHP repository
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt-get update
    
    # Install PHP and extensions
    install_package "php$PHP_VERSION"
    install_package "php$PHP_VERSION-fpm"
    install_package "php$PHP_VERSION-mysql"
    install_package "php$PHP_VERSION-curl"
    install_package "php$PHP_VERSION-gd"
    install_package "php$PHP_VERSION-mbstring"
    install_package "php$PHP_VERSION-xml"
    install_package "php$PHP_VERSION-zip"
    install_package "php$PHP_VERSION-bcmath"
    install_package "php$PHP_VERSION-json"
    install_package "php$PHP_VERSION-opcache"
    install_package "php$PHP_VERSION-redis"
    
    # Install Composer
    if ! command_exists composer; then
        log "Installing Composer..."
        curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
    fi
    
    log "PHP installation completed"
}

# Function to install MySQL
install_mysql() {
    log "Installing MySQL..."
    
    # Install MySQL
    install_package "mysql-server"
    install_package "mysql-client"
    
    # Secure MySQL installation
    log "Securing MySQL installation..."
    sudo mysql_secure_installation
    
    log "MySQL installation completed"
}

# Function to install Nginx
install_nginx() {
    log "Installing Nginx..."
    
    install_package "nginx"
    
    # Start and enable Nginx
    sudo systemctl start nginx
    sudo systemctl enable nginx
    
    log "Nginx installation completed"
}

# Function to configure Nginx
configure_nginx() {
    log "Configuring Nginx for $DOMAIN..."
    
    # Create Nginx configuration
    local nginx_conf="/etc/nginx/sites-available/$DOMAIN"
    
    sudo tee "$nginx_conf" > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    
    # Redirect HTTP to HTTPS
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Security headers
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self' https:; frame-src 'none'; object-src 'none';";
    
    # Root directory
    root $APP_DIR/public;
    index index.html index.php;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;
    
    # API routes (PHP backend)
    location /api {
        try_files \$uri \$uri/ /api/index.php?\$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
        }
    }
    
    # Static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Vue.js SPA routing
    location / {
        try_files \$uri \$uri/ /index.html;
    }
    
    # Security: Deny access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    location ~ ~$ {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Logs
    access_log /var/log/nginx/$DOMAIN.access.log;
    error_log /var/log/nginx/$DOMAIN.error.log;
}
EOF
    
    # Enable site
    sudo ln -sf "$nginx_conf" "/etc/nginx/sites-enabled/$DOMAIN"
    
    # Test Nginx configuration
    sudo nginx -t
    
    # Reload Nginx
    sudo systemctl reload nginx
    
    log "Nginx configuration completed"
}

# Function to install SSL certificate
install_ssl() {
    log "Installing SSL certificate for $DOMAIN..."
    
    # Install Certbot
    install_package "certbot"
    install_package "python3-certbot-nginx"
    
    # Obtain SSL certificate
    sudo certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos --email admin@$DOMAIN
    
    # Set up auto-renewal
    sudo crontab -l 2>/dev/null | { cat; echo "0 12 * * * /usr/bin/certbot renew --quiet"; } | sudo crontab -
    
    log "SSL certificate installation completed"
}

# Function to create database
create_database() {
    log "Creating database for $APP_NAME..."
    
    local db_name="${APP_NAME}_db"
    local db_user="${APP_NAME}_user"
    local db_password=$(openssl rand -base64 32)
    
    # Create database and user
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    sudo mysql -e "CREATE USER IF NOT EXISTS '$db_user'@'localhost' IDENTIFIED BY '$db_password';"
    sudo mysql -e "GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost';"
    sudo mysql -e "FLUSH PRIVILEGES;"
    
    # Save database credentials
    local env_file="$APP_DIR/.env"
    echo "DB_HOST=localhost" >> "$env_file"
    echo "DB_NAME=$db_name" >> "$env_file"
    echo "DB_USER=$db_user" >> "$env_file"
    echo "DB_PASS=$db_password" >> "$env_file"
    
    log "Database created: $db_name"
    log "Database credentials saved to: $env_file"
}

# Function to deploy application
deploy_application() {
    log "Deploying $APP_NAME application..."
    
    # Create application directory
    create_directory "$APP_DIR"
    
    # Copy application files
    log "Copying application files..."
    cp -r . "$APP_DIR/"
    
    # Set proper permissions
    sudo chown -R www-data:www-data "$APP_DIR"
    sudo chmod -R 755 "$APP_DIR"
    sudo chmod -R 775 "$APP_DIR/storage"
    sudo chmod -R 775 "$APP_DIR/public/uploads"
    
    # Install Node.js dependencies and build frontend
    log "Installing Node.js dependencies and building frontend..."
    cd "$APP_DIR"
    npm ci --production
    npm run build
    
    # Install PHP dependencies
    log "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader
    
    # Run database migrations
    log "Running database migrations..."
    php artisan migrate --force
    
    # Clear caches
    log "Clearing application caches..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    # Optimize application
    log "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    log "Application deployment completed"
}

# Function to configure PHP-FPM
configure_php_fpm() {
    log "Configuring PHP-FPM..."
    
    local php_ini="/etc/php/$PHP_VERSION/fpm/php.ini"
    local fpm_conf="/etc/php/$PHP_VERSION/fpm/pool.d/www.conf"
    
    # Optimize PHP settings
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 64M/' "$php_ini"
    sudo sed -i 's/post_max_size = 8M/post_max_size = 64M/' "$php_ini"
    sudo sed -i 's/memory_limit = 128M/memory_limit = 512M/' "$php_ini"
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/' "$php_ini"
    sudo sed -i 's/;opcache.enable=1/opcache.enable=1/' "$php_ini"
    sudo sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' "$php_ini"
    
    # Configure FPM pool
    sudo sed -i 's/pm = dynamic/pm = ondemand/' "$fpm_conf"
    sudo sed -i 's/pm.max_children = 5/pm.max_children = 10/' "$fpm_conf"
    sudo sed -i 's/pm.start_servers = 2/pm.start_servers = 3/' "$fpm_conf"
    sudo sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 2/' "$fpm_conf"
    sudo sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 5/' "$fpm_conf"
    
    # Restart PHP-FPM
    sudo systemctl restart php$PHP_VERSION-fpm
    
    log "PHP-FPM configuration completed"
}

# Function to set up monitoring
setup_monitoring() {
    log "Setting up monitoring..."
    
    # Install monitoring tools
    install_package "htop"
    install_package "iotop"
    install_package "nethogs"
    
    # Create monitoring script
    local monitor_script="/usr/local/bin/monitor-$APP_NAME.sh"
    sudo tee "$monitor_script" > /dev/null <<'EOF'
#!/bin/bash
# Monitoring script for Billing Pages

echo "=== System Resources ==="
echo "CPU Usage: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)%"
echo "Memory Usage: $(free | grep Mem | awk '{printf("%.2f%%", $3/$2 * 100.0)}')"
echo "Disk Usage: $(df -h / | awk 'NR==2{print $5}')"

echo -e "\n=== Application Status ==="
echo "Nginx: $(systemctl is-active nginx)"
echo "PHP-FPM: $(systemctl is-active php8.2-fpm)"
echo "MySQL: $(systemctl is-active mysql)"

echo -e "\n=== Recent Logs ==="
tail -n 10 /var/log/nginx/billing-pages.com.error.log
EOF
    
    sudo chmod +x "$monitor_script"
    
    log "Monitoring setup completed"
}

# Function to create deployment script
create_deployment_script() {
    log "Creating deployment script..."
    
    local deploy_script="/usr/local/bin/deploy-$APP_NAME.sh"
    sudo tee "$deploy_script" > /dev/null <<EOF
#!/bin/bash
# Deployment script for $APP_NAME

set -e

APP_DIR="$APP_DIR"
BACKUP_DIR="$BACKUP_DIR"
APP_NAME="$APP_NAME"

# Create backup
if [[ -d "\$APP_DIR" ]]; then
    echo "Creating backup..."
    backup_name="\${APP_NAME}_backup_\$(date +%Y%m%d_%H%M%S)"
    cp -r "\$APP_DIR" "\$BACKUP_DIR/\$backup_name"
fi

# Pull latest changes
cd "\$APP_DIR"
git pull origin main

# Install dependencies and build
npm ci --production
npm run build

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and optimize caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data "\$APP_DIR"
chmod -R 755 "\$APP_DIR"
chmod -R 775 "\$APP_DIR/storage"
chmod -R 775 "\$APP_DIR/public/uploads"

# Reload services
systemctl reload nginx
systemctl reload php$PHP_VERSION-fpm

echo "Deployment completed successfully!"
EOF
    
    sudo chmod +x "$deploy_script"
    
    log "Deployment script created: $deploy_script"
}

# Function to create systemd service
create_systemd_service() {
    log "Creating systemd service..."
    
    local service_file="/etc/systemd/system/$APP_NAME.service"
    sudo tee "$service_file" > /dev/null <<EOF
[Unit]
Description=Billing Pages Application
After=network.target mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$APP_DIR
ExecStart=/usr/bin/php artisan serve --host=127.0.0.1 --port=8000
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF
    
    sudo systemctl daemon-reload
    sudo systemctl enable "$APP_NAME"
    
    log "Systemd service created"
}

# Function to set up firewall
setup_firewall() {
    log "Setting up firewall..."
    
    # Install UFW if not present
    install_package "ufw"
    
    # Configure firewall
    sudo ufw --force reset
    sudo ufw default deny incoming
    sudo ufw default allow outgoing
    sudo ufw allow ssh
    sudo ufw allow 80/tcp
    sudo ufw allow 443/tcp
    sudo ufw --force enable
    
    log "Firewall configuration completed"
}

# Function to create maintenance script
create_maintenance_script() {
    log "Creating maintenance script..."
    
    local maintenance_script="/usr/local/bin/maintenance-$APP_NAME.sh"
    sudo tee "$maintenance_script" > /dev/null <<'EOF'
#!/bin/bash
# Maintenance script for Billing Pages

APP_DIR="/var/www/vhosts/billing-pages.com"

echo "Starting maintenance..."

# Update system packages
apt-get update
apt-get upgrade -y

# Clean up old backups (keep last 5)
find /var/www/backups -name "billing-pages_backup_*" -type d -mtime +7 -exec rm -rf {} \;

# Clean up old log files
find /var/log -name "*.log" -mtime +30 -delete
find /var/log -name "*.gz" -mtime +30 -delete

# Optimize database
mysql -e "OPTIMIZE TABLE billing_pages_db.*;"

# Clear application caches
cd "$APP_DIR"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl restart mysql

echo "Maintenance completed!"
EOF
    
    sudo chmod +x "$maintenance_script"
    
    # Add to crontab (run weekly)
    sudo crontab -l 2>/dev/null | { cat; echo "0 2 * * 0 /usr/local/bin/maintenance-$APP_NAME.sh >> /var/log/maintenance.log 2>&1"; } | sudo crontab -
    
    log "Maintenance script created: $maintenance_script"
}

# Main deployment function
main() {
    log "Starting Billing Pages deployment..."
    log "Domain: $DOMAIN"
    log "Application: $APP_NAME"
    log "Node.js Version: $NODE_VERSION"
    log "PHP Version: $PHP_VERSION"
    
    # Update system
    log "Updating system packages..."
    sudo apt-get update
    sudo apt-get upgrade -y
    
    # Install required packages
    install_package "curl"
    install_package "wget"
    install_package "git"
    install_package "unzip"
    install_package "software-properties-common"
    install_package "apt-transport-https"
    install_package "ca-certificates"
    install_package "gnupg"
    install_package "lsb-release"
    
    # Backup existing installation
    backup_existing
    
    # Install components
    install_nodejs
    install_php
    install_mysql
    install_nginx
    
    # Deploy application
    deploy_application
    
    # Configure services
    configure_nginx
    configure_php_fpm
    create_database
    
    # Install SSL certificate
    install_ssl
    
    # Set up additional features
    setup_monitoring
    setup_firewall
    create_deployment_script
    create_systemd_service
    create_maintenance_script
    
    # Final configuration
    log "Performing final configuration..."
    
    # Set up log rotation
    sudo tee "/etc/logrotate.d/$APP_NAME" > /dev/null <<EOF
$APP_DIR/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF
    
    # Create health check endpoint
    sudo tee "$APP_DIR/public/health.php" > /dev/null <<'EOF'
<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'healthy',
    'timestamp' => date('c'),
    'version' => '2.0.0'
]);
EOF
    
    log "Deployment completed successfully!"
    log "Application URL: https://$DOMAIN"
    log "Health Check: https://$DOMAIN/health.php"
    log "Monitoring Script: /usr/local/bin/monitor-$APP_NAME.sh"
    log "Deployment Script: /usr/local/bin/deploy-$APP_NAME.sh"
    log "Maintenance Script: /usr/local/bin/maintenance-$APP_NAME.sh"
    
    # Display next steps
    echo -e "${GREEN}"
    echo "=== DEPLOYMENT COMPLETED ==="
    echo "Your Billing Pages application has been successfully deployed!"
    echo ""
    echo "Next steps:"
    echo "1. Visit https://$DOMAIN to access your application"
    echo "2. Run the monitoring script: sudo /usr/local/bin/monitor-$APP_NAME.sh"
    echo "3. Set up your domain DNS to point to this server"
    echo "4. Configure your backup strategy"
    echo "5. Set up monitoring and alerting"
    echo ""
    echo "For future deployments, use: sudo /usr/local/bin/deploy-$APP_NAME.sh"
    echo -e "${NC}"
}

# Run main function
main "$@" 