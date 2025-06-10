# Billing Portal

A modern, secure, and professional billing portal for managing clients, invoices, and payments. Built with PHP and MySQL, this application provides a comprehensive solution for businesses to handle their billing operations efficiently.

## 🚀 Features

### Core Functionality
- **User Management**
  - Secure authentication system
  - Role-based access control
  - User profile management
  - Password reset functionality

- **Client Management**
  - Add, edit, and delete clients
  - Client history tracking
  - Contact information management
  - Client-specific notes and details

- **Invoice System**
  - Create and manage invoices
  - PDF generation
  - Invoice status tracking
  - Payment processing
  - Invoice templates

- **Financial Tools**
  - Payment tracking
  - Financial reports
  - Revenue analytics
  - Export capabilities

### Technical Features
- Responsive design for all devices
- PDF generation

## 📋 Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- SSL certificate (for production)
- Composer (for dependencies)

### PHP Extensions
- PDO
- JSON
- MBString
- OpenSSL
- FileInfo

## 🛠️ Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/DominicBilke/website-billing-pages.git
   cd website-billing-pages/billing pages current/billing_pages/
   ```

2. **Database Setup**
   ```sql
   CREATE DATABASE billing_portal;
   mysql -u your_username -p billing_portal < database/migrations.sql
   ```

3. **Configuration**
   - Update database credentials and settings in `inc/config.php`
   - Configure email settings
   - Set up your domain and SSL

4. **Dependencies**
   ```bash
   composer install
   ```

5. **Initial Setup**
   ```sql
   INSERT INTO users (username, email, password, role) 
   VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
   ```
   Default credentials:
   - Username: `admin`
   - Password: `password`

6. **Web Server Configuration**

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

## 📁 Project Structure

```
billing-portal/
├── database/           # Database migrations and schema
├── inc/               # Core includes and configuration
│   ├── config.php     # Application configuration
│   ├── db.php         # Database connection
│   ├── header.php     # Common header
│   └── footer.php     # Common footer
├── public/            # Public-facing files
│   ├── auth/          # Authentication
│   ├── admin/         # Admin panel
│   ├── clients/       # Client management
│   ├── invoices/      # Invoice system
│   ├── reports/       # Reporting
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   └── images/       # Media files
├── vendor/            # Composer dependencies
├── logs/             # Application logs
└── config/           # Configuration files
```

## 🔒 Security Features

- Password hashing with bcrypt
- CSRF protection on all forms
- XSS protection
- SQL injection prevention
- Secure session handling
- Input validation and sanitization
- Security headers
- Rate limiting

## 💻 Development

1. **Branch Creation**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Development Workflow**
   ```bash
   # Make changes
   git add .
   git commit -m "Add your feature"
   git push origin feature/your-feature-name
   ```

3. **Testing**
   ```bash
   composer test
   ```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.