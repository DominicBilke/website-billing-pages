# Billing Pages - Professional Invoice Management System

A modern, secure, and feature-rich billing and invoice management system built with PHP and MySQL. Perfect for businesses looking to streamline their invoicing process.

## 🚀 Features

### Core Functionality
- **User Management**
  - Secure authentication with email verification
  - Role-based access control (Admin/User)
  - Password reset functionality
  - Remember me functionality
  - Account lockout protection

- **Client Management**
  - Add, edit, and delete clients
  - Client history tracking
  - Contact information management
  - Client-specific notes and details
  - Client status management

- **Invoice System**
  - Create and manage invoices
  - Professional PDF generation
  - Invoice status tracking (Draft, Sent, Paid, Overdue, Cancelled)
  - Payment processing with Stripe integration
  - Customizable invoice templates
  - Automated payment reminders

- **Financial Tools**
  - Payment tracking
  - Financial reports and analytics
  - Revenue analytics with charts
  - Export capabilities
  - Monthly revenue tracking

### Technical Features
- **Modern Architecture**
  - PHP 8.1+ with modern practices
  - Environment-based configuration
  - Secure database abstraction layer
  - Comprehensive error handling
  - Activity logging system

- **Security**
  - CSRF protection on all forms
  - XSS protection
  - SQL injection prevention
  - Secure session handling
  - Input validation and sanitization
  - Security headers
  - Rate limiting for login attempts

- **User Experience**
  - Responsive design for all devices
  - Modern Bootstrap 5 interface
  - Interactive charts and analytics
  - Real-time notifications
  - Intuitive navigation

## 📋 Requirements

### Server Requirements
- **PHP**: 8.1 or higher
- **MySQL**: 8.0+ or MariaDB 10.6+
- **Web Server**: Apache 2.4+ or Nginx
- **SSL Certificate**: Required for production
- **Composer**: For dependency management

### PHP Extensions
- PDO
- PDO MySQL
- JSON
- MBString
- OpenSSL
- FileInfo
- GD (for image processing)
- ZIP (for file uploads)

## 🛠️ Installation

### Quick Start (Development)

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-repo/billing-pages.git
   cd billing-pages
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp env.example .env
   # Edit .env with your configuration
   ```

4. **Database Setup**
   ```sql
   CREATE DATABASE billing_portal;
   mysql -u your_username -p billing_portal < database/schema.sql
   ```

5. **Web Server Configuration**
   - Point document root to `public/` directory
   - Ensure `.htaccess` is enabled
   - Configure SSL certificate

### Production Deployment

For production deployment on Ubuntu 24.04 with Plesk, see the comprehensive [DEPLOYMENT.md](DEPLOYMENT.md) guide.

## 📁 Project Structure

```
billing-pages/
├── database/           # Database schema and migrations
│   └── schema.sql     # Complete database structure
├── inc/               # Core includes and configuration
│   ├── config.php     # Application configuration
│   ├── db.php         # Database connection and utilities
│   ├── auth.php       # Authentication system
│   └── helpers.php    # Utility functions
├── public/            # Public-facing files
│   ├── auth/          # Authentication pages
│   ├── css/           # Stylesheets
│   ├── js/            # JavaScript files
│   ├── images/        # Media files
│   ├── .htaccess      # Apache configuration
│   └── index.php      # Main entry point
├── vendor/            # Composer dependencies
├── logs/              # Application logs
├── .env               # Environment configuration
├── env.example        # Environment template
└── composer.json      # Dependencies
```

## 🔧 Configuration

### Environment Variables

Create a `.env` file based on `env.example`:

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
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
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
APP_KEY=base64:your-32-character-random-key
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Payment Gateway Settings (Stripe)
STRIPE_PUBLISHABLE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

### Web Server Configuration

**Apache (.htaccess)**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**Nginx**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🔒 Security Features

- **Authentication Security**
  - Password hashing with bcrypt
  - Account lockout after failed attempts
  - Email verification required
  - Secure session management
  - Remember me functionality

- **Data Protection**
  - CSRF tokens on all forms
  - Input sanitization and validation
  - SQL injection prevention
  - XSS protection
  - Secure file upload handling

- **Server Security**
  - Security headers
  - HTTPS enforcement
  - Sensitive file protection
  - Rate limiting
  - Activity logging

## 💳 Payment Integration

The system includes Stripe integration for online payments:

1. **Setup Stripe Account**
   - Create account at [stripe.com](https://stripe.com)
   - Get API keys from dashboard

2. **Configure Environment**
   ```env
   STRIPE_PUBLISHABLE_KEY=pk_test_...
   STRIPE_SECRET_KEY=sk_test_...
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

3. **Webhook Configuration**
   - Set webhook URL: `https://billing-pages.com/webhooks/stripe`
   - Configure events: `invoice.payment_succeeded`, `invoice.payment_failed`

## 📊 Reporting & Analytics

- **Dashboard Analytics**
  - Monthly revenue charts
  - Invoice status overview
  - Client activity tracking
  - Payment trends

- **Financial Reports**
  - Revenue reports
  - Outstanding invoices
  - Payment history
  - Client payment analysis

## 🚀 Performance Optimization

- **Caching**
  - OPcache for PHP
  - Static asset caching
  - Database query optimization

- **Compression**
  - Gzip compression enabled
  - Minified CSS/JS
  - Optimized images

- **Database**
  - Indexed queries
  - Connection pooling
  - Query optimization

## 🔧 Maintenance

### Regular Tasks
- Monitor error logs
- Backup database and files
- Update dependencies
- Review security logs
- Check disk space

### Backup Strategy
```bash
# Database backup
mysqldump -u username -p billing_portal > backup.sql

# File backup
tar -czf files_backup.tar.gz /path/to/billing-pages
```

## 🐛 Troubleshooting

### Common Issues

1. **"Primary script unknown" Error**
   - Check document root configuration
   - Verify .htaccess file exists
   - Ensure mod_rewrite is enabled

2. **Database Connection Error**
   - Verify database credentials
   - Check database server status
   - Ensure database exists

3. **Email Not Working**
   - Check SMTP settings
   - Verify email credentials
   - Check mail server logs

4. **File Upload Issues**
   - Check PHP upload limits
   - Verify directory permissions
   - Check disk space

See [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for detailed solutions.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check the guides in this repository
- **Issues**: Report bugs via GitHub Issues
- **Security**: Report security issues privately
- **Community**: Join our community discussions

## 🗺️ Roadmap

### Upcoming Features
- [ ] Multi-currency support
- [ ] Advanced reporting
- [ ] API for integrations
- [ ] Mobile app
- [ ] White-label options
- [ ] Advanced automation
- [ ] Multi-language support

### Version History
- **v2.0.0** - Complete rewrite with modern architecture
- **v1.0.0** - Initial release

---

**Billing Pages** - Professional invoice management made simple.