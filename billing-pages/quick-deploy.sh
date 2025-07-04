#!/bin/bash

# Billing Pages - Quick Deployment Script
# For instant deployment on Ubuntu 24.04

set -e

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
DOMAIN="billing-pages.com"
APP_DIR="/var/www/vhosts/$DOMAIN"

log() {
    echo -e "${GREEN}[$(date +'%H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "Run as regular user with sudo privileges"
fi

log "🚀 Starting Billing Pages Quick Deployment"

# Update system
log "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install essential packages
log "🔧 Installing essential packages..."
sudo apt install -y curl wget git unzip nginx mysql-server php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-json php8.2-opcache

# Install Node.js
log "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
log "📦 Installing Composer..."
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Create application directory
log "📁 Creating application directory..."
sudo mkdir -p "$APP_DIR"
sudo chown $USER:$USER "$APP_DIR"

# Copy application files
log "📋 Copying application files..."
cp -r . "$APP_DIR/"

# Install dependencies
log "📦 Installing dependencies..."
cd "$APP_DIR"
npm ci --production
composer install --no-dev --optimize-autoloader

# Build frontend
log "🔨 Building frontend..."
npm run build

# Create database
log "🗄️ Setting up database..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS billing_pages_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'billing_user'@'localhost' IDENTIFIED BY 'billing_password_123';"
sudo mysql -e "GRANT ALL PRIVILEGES ON billing_pages_db.* TO 'billing_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Configure environment
log "⚙️ Configuring environment..."
cp env.example .env
sed -i 's/DB_DATABASE=billing_pages_db/DB_DATABASE=billing_pages_db/' .env
sed -i 's/DB_USERNAME=billing_pages_user/DB_USERNAME=billing_user/' .env
sed -i 's/DB_PASSWORD=your_secure_password/DB_PASSWORD=billing_password_123/' .env
sed -i 's/APP_URL=https:\/\/billing-pages.com/APP_URL=http:\/\/localhost/' .env

# Set permissions
log "🔐 Setting permissions..."
sudo chown -R www-data:www-data "$APP_DIR"
sudo chmod -R 755 "$APP_DIR"
sudo chmod -R 775 "$APP_DIR/storage" 2>/dev/null || true

# Configure Nginx
log "🌐 Configuring Nginx..."
sudo tee "/etc/nginx/sites-available/$DOMAIN" > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN localhost;
    
    root $APP_DIR/public;
    index index.html index.php;
    
    location / {
        try_files \$uri \$uri/ /index.html;
    }
    
    location /api {
        try_files \$uri \$uri/ /api/index.php?\$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
        }
    }
    
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Enable site
sudo ln -sf "/etc/nginx/sites-available/$DOMAIN" "/etc/nginx/sites-enabled/"
sudo rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
sudo nginx -t
sudo systemctl reload nginx

# Start services
log "🚀 Starting services..."
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
sudo systemctl start mysql
sudo systemctl enable mysql

# Configure firewall
log "🔥 Configuring firewall..."
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable

log "✅ Deployment completed successfully!"
log "🌐 Access your application at: http://localhost"
log "📧 Default credentials: admin@billing-pages.com / password"
log "🗄️ Database: billing_pages_db"
log "🔧 To customize, edit: $APP_DIR/.env"

echo -e "${GREEN}"
echo "🎉 Billing Pages is now running!"
echo ""
echo "Next steps:"
echo "1. Visit http://localhost to access your application"
echo "2. Update the .env file with your domain and settings"
echo "3. Set up SSL certificate for production"
echo "4. Configure your backup strategy"
echo -e "${NC}" 