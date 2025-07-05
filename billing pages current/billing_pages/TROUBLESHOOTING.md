# Apache "Primary script unknown" Error - Troubleshooting Guide

## Problem
The error `AH01071: Got error 'Primary script unknown'` occurs when Apache cannot find the main PHP script to execute.

## Solutions

### 1. Verify Document Root Configuration
Make sure your Apache virtual host is configured to point to the `public` directory:

```apache
DocumentRoot "/path/to/your/billing pages current/billing_pages/public"
```

### 2. Check File Permissions
Ensure the web server has read access to the files:
```bash
chmod 755 /path/to/your/billing pages current/billing_pages/public
chmod 644 /path/to/your/billing pages current/billing_pages/public/*.php
```

### 3. Verify .htaccess File
The `.htaccess` file has been created in the `public` directory. Make sure it contains:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

### 4. Enable Required Apache Modules
Ensure these Apache modules are enabled:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires
sudo a2enmod deflate
sudo systemctl restart apache2
```

### 5. Test PHP Processing
Visit `https://billing-pages.com/test.php` to verify PHP is working.

### 6. Check Apache Error Logs
View Apache error logs for more details:
```bash
sudo tail -f /var/log/apache2/error.log
```

### 7. Verify Virtual Host Configuration
Use the provided `apache-vhost.conf` file as a template and update the paths:
- Replace `/path/to/your/` with your actual server path
- Update SSL certificate paths if using HTTPS

### 8. Restart Apache
After making changes:
```bash
sudo systemctl restart apache2
```

## Common Issues

### Issue: Apache not serving from public directory
**Solution**: Update virtual host DocumentRoot to point to the `public` folder, not the root project folder.

### Issue: .htaccess not being read
**Solution**: Ensure `AllowOverride All` is set in the Directory directive.

### Issue: PHP not processing
**Solution**: Verify PHP module is installed and enabled in Apache.

### Issue: File permissions
**Solution**: Ensure web server user (www-data, apache, etc.) has read access to files.

## Testing Steps

1. Visit `https://billing-pages.com/test.php` - should show PHP info
2. Visit `https://billing-pages.com/` - should show the billing portal homepage
3. Check Apache error logs if issues persist

## Support
If the issue persists, check:
- Apache configuration syntax: `apache2ctl configtest`
- PHP error logs
- File system permissions
- DNS resolution for billing-pages.com 