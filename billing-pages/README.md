# Billing Pages - Modern Billing Portal

A modern, secure, and professional billing portal system based on the abrechnung-portal.de architecture, designed for deployment on Ubuntu 24.04 with Plesk.

## 🚀 Features

### Core Functionality
- **Multi-Tenant Architecture** - Separate domains and databases for different billing types
- **User Management** - Secure authentication with role-based access control
- **Multiple Billing Modules**:
  - Company Billing (Firmenabrechnung)
  - Tour Billing (Tourenabrechnung) 
  - Work Billing (Arbeitsabrechnung)
  - Task Billing (Aufgabenabrechnung)
  - Money Billing (Geldabrechnung)

### Technical Features
- **File Management** - Upload and store various file types (PDF, images, GPX)
- **Reporting** - PDF generation, charts, and analytics
- **Geographic Tracking** - GPX file support with map visualization
- **Responsive Design** - Bootstrap 5.2.0 interface
- **Security** - Session-based authentication, input validation

## 📋 System Requirements

### Server Requirements
- Ubuntu 24.04 LTS
- Plesk Obsidian or newer
- PHP 8.1+ with required extensions
- MySQL 8.0+ or MariaDB 10.6+
- Apache 2.4+ with mod_rewrite
- SSL certificate (Let's Encrypt recommended)

### PHP Extensions
- PDO and PDO_MySQL
- JSON
- MBString
- OpenSSL
- FileInfo
- GD (for image processing)
- ZIP (for file operations)

## 🛠️ Installation on Ubuntu 24.04 with Plesk

### 1. Plesk Preparation
```bash
# Access Plesk via SSH or web interface
# Create a new domain: billing-pages.com
# Enable PHP 8.1+ in domain settings
# Enable SSL certificate
```

### 2. Database Setup
```sql
-- Create main database
CREATE DATABASE billing_pages_main CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create domain-specific databases
CREATE DATABASE billing_pages_companies CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_tours CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_work CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_tasks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE billing_pages_money CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database users
CREATE USER 'billing_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON billing_pages_*.* TO 'billing_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. File Upload and Setup
```bash
# Upload project files to domain root
# Set proper permissions
chmod 755 /var/www/vhosts/billing-pages.com/httpdocs/
chmod 644 /var/www/vhosts/billing-pages.com/httpdocs/.htaccess
chmod -R 755 /var/www/vhosts/billing-pages.com/httpdocs/uploads/
chmod -R 755 /var/www/vhosts/billing-pages.com/httpdocs/temp/

# Set ownership
chown -R psaserv:psaserv /var/www/vhosts/billing-pages.com/httpdocs/
```

### 4. Configuration
```bash
# Edit configuration files
nano /var/www/vhosts/billing-pages.com/httpdocs/config/database.php
nano /var/www/vhosts/billing-pages.com/httpdocs/config/app.php
```

### 5. Database Migration
```bash
# Run database setup scripts
mysql -u billing_user -p billing_pages_main < database/schema.sql
mysql -u billing_user -p billing_pages_companies < database/companies_schema.sql
mysql -u billing_user -p billing_pages_tours < database/tours_schema.sql
mysql -u billing_user -p billing_pages_work < database/work_schema.sql
mysql -u billing_user -p billing_pages_tasks < database/tasks_schema.sql
mysql -u billing_user -p billing_pages_money < database/money_schema.sql
```

### 6. Plesk Configuration
- Enable Apache mod_rewrite
- Configure PHP settings in Plesk
- Set up SSL certificate
- Configure backup settings

## 📁 Project Structure

```
billing-pages/
├── config/                 # Configuration files
│   ├── database.php       # Database connections
│   ├── app.php           # Application settings
│   └── security.php      # Security settings
├── database/              # Database schemas and migrations
│   ├── schema.sql        # Main database schema
│   ├── companies_schema.sql
│   ├── tours_schema.sql
│   ├── work_schema.sql
│   ├── tasks_schema.sql
│   └── money_schema.sql
├── public/                # Public web files
│   ├── index.php         # Main entry point
│   ├── .htaccess         # URL rewriting
│   ├── assets/           # Static assets
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── uploads/          # File uploads
├── src/                   # Application source code
│   ├── Controllers/      # Controller classes
│   ├── Models/           # Data models
│   ├── Services/         # Business logic
│   ├── Utils/            # Utility functions
│   └── Views/            # Template files
├── vendor/               # Composer dependencies
├── logs/                 # Application logs
├── temp/                 # Temporary files
└── docs/                 # Documentation
```

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection
- XSS prevention
- SQL injection protection
- Secure session handling
- Input validation and sanitization
- File upload security
- Rate limiting

## 🚀 Deployment Checklist

- [ ] Domain configured in Plesk
- [ ] SSL certificate installed
- [ ] PHP 8.1+ enabled
- [ ] Required PHP extensions installed
- [ ] Databases created and configured
- [ ] File permissions set correctly
- [ ] Configuration files updated
- [ ] Database migrations run
- [ ] Test login functionality
- [ ] Test file uploads
- [ ] Test PDF generation
- [ ] Backup system configured

## 📞 Support

For technical support or questions about deployment, please contact the development team.

## 📄 License

This project is licensed under the MIT License. 