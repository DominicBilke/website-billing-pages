#!/bin/bash

# Billing Pages - Quick Deployment Script for Ubuntu 24.04
# This script sets up the complete environment for the billing portal

set -e

echo "🚀 Starting Billing Pages deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Update system
print_status "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install required packages
print_status "Installing required packages..."
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Install Node.js 18.x
print_status "Installing Node.js 18.x..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PHP 8.2
print_status "Installing PHP 8.2..."
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

# Install MySQL
print_status "Installing MySQL..."
sudo apt install -y mysql-server

# Install Nginx
print_status "Installing Nginx..."
sudo apt install -y nginx

# Install Composer
print_status "Installing Composer..."
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Create project directory
PROJECT_DIR="/var/www/billing-pages"
print_status "Setting up project directory: $PROJECT_DIR"

sudo mkdir -p $PROJECT_DIR
sudo chown $USER:$USER $PROJECT_DIR

# Copy project files (assuming script is run from project directory)
print_status "Copying project files..."
cp -r . $PROJECT_DIR/

# Install Node.js dependencies
print_status "Installing Node.js dependencies..."
cd $PROJECT_DIR
npm install

# Build the application
print_status "Building the application..."
npm run build

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Configure MySQL
print_status "Configuring MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS billing_pages CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'billing_user'@'localhost' IDENTIFIED BY 'billing_password_2024';"
sudo mysql -e "GRANT ALL PRIVILEGES ON billing_pages.* TO 'billing_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Import database schema
print_status "Importing database schema..."
sudo mysql billing_pages < database/billing_portal.sql

# Configure Nginx
print_status "Configuring Nginx..."
sudo tee /etc/nginx/sites-available/billing-pages << EOF
server {
    listen 80;
    server_name billing-pages.com www.billing-pages.com;
    root $PROJECT_DIR/public;
    index index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;

    # Handle Vue Router
    location / {
        try_files \$uri \$uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # API routes (if needed)
    location /api/ {
        proxy_pass http://localhost:8000;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ \.(env|log|sql)$ {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/billing-pages /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Configure PHP-FPM
print_status "Configuring PHP-FPM..."
sudo tee /etc/php/8.2/fpm/pool.d/billing-pages.conf << EOF
[billing-pages]
user = www-data
group = www-data
listen = /run/php/php8.2-fpm-billing-pages.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOF

# Set proper permissions
print_status "Setting proper permissions..."
sudo chown -R www-data:www-data $PROJECT_DIR
sudo chmod -R 755 $PROJECT_DIR
sudo chmod -R 775 $PROJECT_DIR/storage

# Create environment file
print_status "Creating environment file..."
sudo tee $PROJECT_DIR/.env << EOF
APP_NAME="Billing Pages"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://billing-pages.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billing_pages
DB_USERNAME=billing_user
DB_PASSWORD=billing_password_2024

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
EOF

# Restart services
print_status "Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl enable php8.2-fpm
sudo systemctl enable nginx

# Configure firewall
print_status "Configuring firewall..."
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw --force enable

# Create systemd service for the application (if needed)
print_status "Creating systemd service..."
sudo tee /etc/systemd/system/billing-pages.service << EOF
[Unit]
Description=Billing Pages Application
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_DIR
ExecStart=/usr/bin/php -S localhost:8000 -t public
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Enable and start the service
sudo systemctl daemon-reload
sudo systemctl enable billing-pages
sudo systemctl start billing-pages

# Final status check
print_status "Performing final status check..."
if sudo systemctl is-active --quiet nginx; then
    print_success "Nginx is running"
else
    print_error "Nginx is not running"
fi

if sudo systemctl is-active --quiet php8.2-fpm; then
    print_success "PHP-FPM is running"
else
    print_error "PHP-FPM is not running"
fi

if sudo systemctl is-active --quiet mysql; then
    print_success "MySQL is running"
else
    print_error "MySQL is not running"
fi

# Display completion message
echo ""
print_success "🎉 Billing Pages deployment completed successfully!"
echo ""
echo "📋 Deployment Summary:"
echo "   • Project URL: http://billing-pages.com"
echo "   • Project Directory: $PROJECT_DIR"
echo "   • Database: billing_pages"
echo "   • Database User: billing_user"
echo "   • Database Password: billing_password_2024"
echo ""
echo "🔧 Next Steps:"
echo "   1. Configure your domain DNS to point to this server"
echo "   2. Install SSL certificate: sudo certbot --nginx -d billing-pages.com"
echo "   3. Update the .env file with your production settings"
echo "   4. Set up regular backups"
echo ""
echo "📚 Useful Commands:"
echo "   • View logs: sudo journalctl -u nginx -f"
echo "   • Restart services: sudo systemctl restart nginx php8.2-fpm"
echo "   • Check status: sudo systemctl status nginx php8.2-fpm mysql"
echo ""
print_success "Your Billing Pages application is now ready!" 