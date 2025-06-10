# Billing Portal

A professional billing portal for managing clients, invoices, and payments.

## Features

- User authentication and authorization
- Client management
- Invoice creation and management
- Payment tracking
- Reports and analytics
- Email notifications
- PDF generation
- Responsive design
- Dark mode support

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)
- SSL certificate (for production)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/billing-portal.git
cd billing-portal
```

2. Create a MySQL database:
```sql
CREATE DATABASE billing_portal;
```

3. Import the database schema:
```bash
mysql -u your_username -p billing_portal < database/migrations.sql
```

4. Configure the application:
   - Copy `inc/config.example.php` to `inc/config.php`
   - Update the database credentials and other settings in `inc/config.php`

5. Set up the web server:
   - Point the document root to the `public` directory
   - Ensure the `logs` directory is writable
   - Configure URL rewriting (see below)

6. Install dependencies:
```bash
composer install
```

7. Create an admin user:
```sql
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```
Default password: `password`

## URL Rewriting

### Apache
Create a `.htaccess` file in the `public` directory:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

### Nginx
Add to your server configuration:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Directory Structure

```
billing-portal/
├── database/
│   └── migrations.sql
├── inc/
│   ├── config.php
│   ├── db.php
│   ├── header.php
│   └── footer.php
├── logs/
├── public/
│   ├── auth/
│   ├── clients/
│   ├── css/
│   ├── images/
│   ├── invoices/
│   ├── js/
│   └── index.php
├── vendor/
├── .gitignore
├── composer.json
└── README.md
```

## Security

- All passwords are hashed using bcrypt
- CSRF protection on all forms
- XSS protection
- SQL injection prevention using prepared statements
- Secure session handling
- Input validation and sanitization
- File upload restrictions
- Security headers

## Development

1. Create a new branch for your feature:
```bash
git checkout -b feature/your-feature-name
```

2. Make your changes and commit them:
```bash
git add .
git commit -m "Add your feature"
```

3. Push to the remote repository:
```bash
git push origin feature/your-feature-name
```

4. Create a pull request

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@example.com or create an issue in the repository. 