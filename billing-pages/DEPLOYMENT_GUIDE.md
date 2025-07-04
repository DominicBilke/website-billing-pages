# Billing Pages Deployment Guide
## Ubuntu 24.04 with Plesk

This guide provides step-by-step instructions for deploying the Billing Pages application on Ubuntu 24.04 with Plesk.

## Prerequisites

### System Requirements
- Ubuntu 24.04 LTS
- Plesk Obsidian or newer
- Minimum 2GB RAM
- 20GB available disk space
- Root access or sudo privileges

### Required Software
- PHP 8.1 or higher
- MySQL 8.0 or MariaDB 10.6
- Apache 2.4 with mod_rewrite
- Composer
- SSL certificate (Let's Encrypt recommended)

## Step 1: Server Preparation

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Install Required Packages
```bash
sudo apt install -y curl wget git unzip software-properties-common
```

### 1.3 Verify Plesk Installation
```bash
plesk version
```

## Step 2: Domain Setup in Plesk

### 2.1 Create Domain
1. Log into Plesk admin panel
2. Go to "Domains" → "Add Domain"
3. Enter domain name: `billing-pages.com`
4. Select subscription owner
5. Click "OK"

### 2.2 Configure PHP
1. Go to "Domains" → `billing-pages.com` → "PHP Settings"
2. Select PHP 8.1 or higher
3. Set memory limit to 256M
4. Enable required extensions:
   - PDO
   - PDO_MySQL
   - JSON
   - MBString
   - OpenSSL
   - FileInfo
   - GD
   - ZIP

### 2.3 Enable SSL
1. Go to "SSL/TLS Certificates"
2. Click "Let's Encrypt"
3. Select domain and click "Get Certificate"
4. Enable "Force HTTPS"

## Step 3: Database Setup

### 3.1 Create Databases
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create main database
CREATE DATABASE billing_pages_main CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create module databases
CREATE DATABASE billing_pages_companies CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_tours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_work CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_tasks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_money CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user
CREATE USER 'billing_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
GRANT ALL PRIVILEGES ON billing_pages_*.* TO 'billing_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Import Database Schemas
```bash
# Navigate to project directory
cd /path/to/billing-pages

# Import schemas
mysql -u billing_user -p billing_pages_main < database/schema.sql
mysql -u billing_user -p billing_pages_companies < database/companies_schema.sql
mysql -u billing_user -p billing_pages_tours < database/tours_schema.sql
mysql -u billing_user -p billing_pages_work < database/work_schema.sql
mysql -u billing_user -p billing_pages_tasks < database/tasks_schema.sql
mysql -u billing_user -p billing_pages_money < database/money_schema.sql
```

## Step 4: Application Deployment

### 4.1 Upload Files
```bash
# Set web root path
WEB_ROOT="/var/www/vhosts/billing-pages.com/httpdocs"

# Create necessary directories
sudo mkdir -p $WEB_ROOT
sudo mkdir -p $WEB_ROOT/logs
sudo mkdir -p $WEB_ROOT/temp
sudo mkdir -p $WEB_ROOT/uploads

# Copy application files
sudo cp -r public/* $WEB_ROOT/
sudo cp -r src $WEB_ROOT/
sudo cp -r config $WEB_ROOT/
sudo cp -r database $WEB_ROOT/
sudo cp composer.json $WEB_ROOT/
sudo cp composer.lock $WEB_ROOT/ 2>/dev/null || true
```

### 4.2 Set Permissions
```bash
# Set ownership
sudo chown -R psaserv:psaserv $WEB_ROOT

# Set permissions
sudo chmod -R 755 $WEB_ROOT
sudo chmod -R 777 $WEB_ROOT/logs
sudo chmod -R 777 $WEB_ROOT/temp
sudo chmod -R 777 $WEB_ROOT/uploads
```

### 4.3 Install Dependencies
```bash
# Navigate to web root
cd $WEB_ROOT

# Install Composer if not available
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Install dependencies
composer install --no-dev --optimize-autoloader
```

## Step 5: Configuration

### 5.1 Environment Configuration
```bash
# Copy environment template
sudo cp env.example $WEB_ROOT/.env

# Edit environment file
sudo nano $WEB_ROOT/.env
```

Update the following values in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://billing-pages.com

DB_HOST=localhost
DB_PORT=3306
DB_MAIN=billing_pages_main
DB_COMPANIES=billing_pages_companies
DB_TOURS=billing_pages_tours
DB_WORK=billing_pages_work
DB_TASKS=billing_pages_tasks
DB_MONEY=billing_pages_money
DB_USERNAME=billing_user
DB_PASSWORD=your_secure_password_here

MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@billing-pages.com
MAIL_FROM_NAME=Billing Pages
```

### 5.2 Set Environment File Permissions
```bash
sudo chown psaserv:psaserv $WEB_ROOT/.env
sudo chmod 600 $WEB_ROOT/.env
```

## Step 6: Apache Configuration

### 6.1 Enable Required Modules
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl reload apache2
```

### 6.2 Configure Virtual Host
The `.htaccess` file in the public directory should handle most configuration, but you may need to ensure mod_rewrite is enabled in Plesk:

1. Go to "Apache & nginx Settings"
2. Enable "Additional directives for HTTP"
3. Add if needed:
```apache
<Directory /var/www/vhosts/billing-pages.com/httpdocs>
    AllowOverride All
</Directory>
```

## Step 7: Security Configuration

### 7.1 Firewall Setup
```bash
# Allow SSH, HTTP, HTTPS
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### 7.2 SSL Configuration
Ensure SSL is properly configured in Plesk:
1. Go to "SSL/TLS Certificates"
2. Verify certificate is active
3. Enable "Force HTTPS"

### 7.3 File Permissions
```bash
# Secure sensitive files
sudo chmod 600 $WEB_ROOT/.env
sudo chmod 600 $WEB_ROOT/config/database.php
sudo chmod 600 $WEB_ROOT/config/app.php

# Prevent access to sensitive directories
echo "Deny from all" | sudo tee $WEB_ROOT/config/.htaccess
echo "Deny from all" | sudo tee $WEB_ROOT/logs/.htaccess
```

## Step 8: Testing

### 8.1 Test Database Connection
```bash
# Test database connectivity
mysql -u billing_user -p -e "USE billing_pages_main; SELECT 1;"
```

### 8.2 Test Web Access
```bash
# Test HTTP response
curl -I https://billing-pages.com

# Test application
curl -s https://billing-pages.com | grep -q "Billing Pages"
```

### 8.3 Test File Uploads
1. Access the application
2. Try uploading a test file
3. Verify file appears in uploads directory

## Step 9: Backup Configuration

### 9.1 Create Backup Script
```bash
# Create backup directory
sudo mkdir -p /var/www/vhosts/billing-pages.com/backups

# Create backup script
sudo nano /var/www/vhosts/billing-pages.com/backups/backup.sh
```

Add the following content:
```bash
#!/bin/bash
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
```

### 9.2 Set Backup Permissions
```bash
sudo chmod +x /var/www/vhosts/billing-pages.com/backups/backup.sh
sudo chown psaserv:psaserv /var/www/vhosts/billing-pages.com/backups/backup.sh
```

### 9.3 Schedule Backups
```bash
# Add to crontab
sudo crontab -e

# Add this line for daily backups at 2 AM
0 2 * * * /var/www/vhosts/billing-pages.com/backups/backup.sh
```

## Step 10: Monitoring and Maintenance

### 10.1 Log Monitoring
```bash
# Monitor application logs
tail -f /var/www/vhosts/billing-pages.com/httpdocs/logs/app.log

# Monitor PHP errors
tail -f /var/log/php_errors.log

# Monitor Apache logs
tail -f /var/log/apache2/access.log
tail -f /var/log/apache2/error.log
```

### 10.2 Performance Monitoring
```bash
# Monitor disk usage
df -h

# Monitor memory usage
free -h

# Monitor database connections
mysql -e "SHOW PROCESSLIST;"
```

### 10.3 Regular Maintenance
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Clean old log files
sudo find /var/log -name "*.log" -mtime +30 -delete

# Optimize databases
mysqlcheck -u billing_user -p --optimize --all-databases
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
- Verify database credentials in `.env`
- Check if MySQL service is running: `sudo systemctl status mysql`
- Verify database exists: `mysql -u billing_user -p -e "SHOW DATABASES;"`

#### 2. Permission Denied Errors
- Check file ownership: `ls -la /var/www/vhosts/billing-pages.com/httpdocs`
- Fix permissions: `sudo chown -R psaserv:psaserv /var/www/vhosts/billing-pages.com/httpdocs`

#### 3. 500 Internal Server Error
- Check PHP error logs: `tail -f /var/log/php_errors.log`
- Verify `.htaccess` file exists and is readable
- Check Apache error logs: `tail -f /var/log/apache2/error.log`

#### 4. SSL Certificate Issues
- Verify certificate in Plesk
- Check certificate expiration: `openssl x509 -in /path/to/cert.pem -text -noout`
- Renew Let's Encrypt certificate if needed

### Support

For additional support:
1. Check application logs in `/var/www/vhosts/billing-pages.com/httpdocs/logs/`
2. Review Plesk error logs
3. Contact system administrator
4. Refer to application documentation

## Post-Deployment Checklist

- [ ] Domain resolves correctly
- [ ] SSL certificate is active
- [ ] Application loads without errors
- [ ] Database connections work
- [ ] File uploads function properly
- [ ] User authentication works
- [ ] Backup system is configured
- [ ] Monitoring is in place
- [ ] Security measures are implemented
- [ ] Performance is acceptable

## Security Recommendations

1. **Regular Updates**: Keep system and application updated
2. **Strong Passwords**: Use complex passwords for all accounts
3. **Firewall**: Configure firewall rules appropriately
4. **Backups**: Test backup and restore procedures regularly
5. **Monitoring**: Set up log monitoring and alerting
6. **Access Control**: Limit access to sensitive files and directories
7. **SSL**: Always use HTTPS in production
8. **Rate Limiting**: Implement rate limiting for login attempts

## Performance Optimization

1. **Caching**: Enable OPcache for PHP
2. **Database**: Optimize database queries and indexes
3. **CDN**: Use CDN for static assets
4. **Compression**: Enable gzip compression
5. **Images**: Optimize image sizes and formats
6. **Monitoring**: Monitor performance metrics regularly 