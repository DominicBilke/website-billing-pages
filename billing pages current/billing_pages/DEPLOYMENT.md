# Billing Pages Deployment Guide
## Ubuntu 24.04 with Plesk

This guide will help you deploy the Billing Pages application on Ubuntu 24.04 with Plesk.

## Prerequisites

- Ubuntu 24.04 server
- Plesk Obsidian or newer
- PHP 8.1+ with required extensions
- MySQL 8.0+ or MariaDB 10.6+
- SSL certificate (recommended)

## Step 1: Server Preparation

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Install Required PHP Extensions
In Plesk, go to **Tools & Settings > PHP Settings** and ensure these extensions are enabled:
- PDO
- PDO MySQL
- JSON
- MBString
- OpenSSL
- FileInfo
- GD (for image processing)
- ZIP (for file uploads)

### 1.3 Configure PHP Settings
Set these recommended values in your PHP configuration:
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
max_input_vars = 3000
```

## Step 2: Plesk Configuration

### 2.1 Create Domain/Subdomain
1. Log into Plesk
2. Go to **Domains**
3. Click **Add Domain**
4. Enter `billing-pages.com` (or your domain)
5. Set document root to `/httpdocs`

### 2.2 Configure PHP Version
1. Go to **Domains > billing-pages.com > PHP Settings**
2. Select PHP 8.1 or higher
3. Enable required extensions
4. Apply settings

### 2.3 Set Up SSL Certificate
1. Go to **Domains > billing-pages.com > SSL/TLS Certificates**
2. Install Let's Encrypt certificate or upload your own
3. Enable **Force HTTPS**

## Step 3: Application Deployment

### 3.1 Upload Files
1. Go to **File Manager** in Plesk
2. Navigate to `/httpdocs`
3. Upload all files from the `billing_pages` directory
4. Ensure the `public` directory is accessible

### 3.2 Set File Permissions
```bash
# Set directory permissions
find /var/www/vhosts/billing-pages.com/httpdocs -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/vhosts/billing-pages.com/httpdocs -type f -exec chmod 644 {} \;

# Set special permissions for logs and uploads
chmod 755 /var/www/vhosts/billing-pages.com/httpdocs/logs
chmod 755 /var/www/vhosts/billing-pages.com/httpdocs/public/uploads
```

### 3.3 Configure Document Root
1. Go to **Domains > billing-pages.com > Apache & nginx Settings**
2. Set **Document root** to `/httpdocs/public`
3. Save changes

## Step 4: Database Setup

### 4.1 Create Database
1. Go to **Databases** in Plesk
2. Click **Add Database**
3. Create database: `billing_portal`
4. Create user: `billing_user`
5. Set strong password
6. Grant all privileges to the user

### 4.2 Import Schema
1. Go to **phpMyAdmin** or **Database Tools**
2. Select your database
3. Import the `database/schema.sql` file

## Step 5: Environment Configuration

### 5.1 Create Environment File
1. In File Manager, navigate to `/httpdocs`
2. Create `.env` file based on `env.example`
3. Update with your actual values:

```env
# Application Settings
APP_NAME="Billing Pages"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://billing-pages.com
APP_TIMEZONE=UTC

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=billing_portal
DB_USERNAME=billing_user
DB_PASSWORD=your_secure_password
DB_CHARSET=utf8mb4

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@billing-pages.com
MAIL_FROM_NAME="Billing Pages"

# Security Settings
APP_KEY=base64:$(openssl rand -base64 32)
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# File Upload Settings
UPLOAD_MAX_SIZE=5242880
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx

# Payment Gateway Settings (Stripe)
STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### 5.2 Install Dependencies
1. Go to **SSH Terminal** in Plesk
2. Navigate to your domain directory:
```bash
cd /var/www/vhosts/billing-pages.com/httpdocs
composer install --no-dev --optimize-autoloader
```

## Step 6: Apache Configuration

### 6.1 Create .htaccess
The `.htaccess` file should already be in the `public` directory. If not, create it:

```apache
# Enable URL rewriting
RewriteEngine On

# Handle requests for files and directories that don't exist
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Prevent access to sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|conf)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>
```

### 6.2 Enable Apache Modules
In Plesk, ensure these Apache modules are enabled:
- mod_rewrite
- mod_headers
- mod_deflate
- mod_expires

## Step 7: Security Configuration

### 7.1 Set Up Firewall
```bash
# Allow HTTP and HTTPS
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### 7.2 Configure Fail2ban
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 7.3 Set Up Backup
1. In Plesk, go to **Tools & Settings > Backup Manager**
2. Create backup schedule for:
   - Files
   - Database
   - Configuration

## Step 8: Testing

### 8.1 Test Application
1. Visit `https://billing-pages.com`
2. Test registration and login
3. Create a test invoice
4. Verify email functionality

### 8.2 Check Logs
Monitor these log files:
- `/var/www/vhosts/billing-pages.com/httpdocs/logs/error.log`
- Apache error logs in Plesk
- PHP error logs

## Step 9: Performance Optimization

### 9.1 Enable OPcache
In PHP settings, enable and configure OPcache:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 9.2 Configure Redis (Optional)
For better performance, install Redis:
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
```

### 9.3 Set Up CDN
Consider using a CDN like Cloudflare for static assets.

## Step 10: Monitoring

### 10.1 Set Up Monitoring
1. Configure Plesk monitoring
2. Set up uptime monitoring
3. Configure email alerts

### 10.2 Regular Maintenance
- Update PHP and dependencies regularly
- Monitor disk space and logs
- Backup database and files
- Review security logs

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check file permissions
   - Verify .htaccess configuration
   - Check PHP error logs

2. **Database Connection Error**
   - Verify database credentials in .env
   - Check database server status
   - Ensure database user has proper permissions

3. **Email Not Working**
   - Verify SMTP settings
   - Check mail server logs
   - Test with different email provider

4. **File Upload Issues**
   - Check upload_max_filesize in PHP settings
   - Verify directory permissions
   - Check disk space

### Support
For additional support:
1. Check the application logs
2. Review Plesk error logs
3. Contact your hosting provider
4. Check the troubleshooting guide in the application

## Security Checklist

- [ ] SSL certificate installed and enforced
- [ ] Strong database passwords
- [ ] File permissions properly set
- [ ] Sensitive files protected
- [ ] Regular backups configured
- [ ] Firewall enabled
- [ ] Fail2ban configured
- [ ] PHP security settings optimized
- [ ] Monitoring and alerting set up

## Performance Checklist

- [ ] OPcache enabled
- [ ] Static asset caching configured
- [ ] Gzip compression enabled
- [ ] Database optimized
- [ ] CDN configured (optional)
- [ ] Regular maintenance scheduled

Your Billing Pages application should now be fully deployed and ready for production use! 