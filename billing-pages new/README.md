# Billing System Portal

A comprehensive web-based billing system for managing companies, tours, tasks, work entries, and money transactions.

## Features

- User authentication and authorization
- Company management
- Tour management
- Task tracking and billing
- Work entry tracking
- Money transaction tracking
- Reports and evaluations
- Export functionality
- Activity logging

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for dependencies)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd billing-pages
```

2. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p < create_billing_tables.sql
```

3. Configure the database connection:
   - Copy `script/config.example.php` to `script/config.php`
   - Update the database credentials in `config.php`

4. Set up the web server:
   - Point the document root to the `billing-pages` directory
   - Ensure the web server has write permissions for upload directories

5. Install dependencies:
```bash
composer install
```

6. Set up file permissions:
```bash
chmod 755 -R .
chmod 777 -R tasks_upload/
chmod 777 -R company_upload/
```

## Default Login

- Username: admin
- Password: admin123

**Important**: Change the default password after first login!

## Directory Structure

```
billing-pages/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── script/
│   ├── config.php
│   ├── database.php
│   ├── auth.php
│   └── functions.php
├── templates/
│   └── base.php
├── tasks_upload/
├── company_upload/
└── *.php
```

## Usage

1. Login with your credentials
2. Navigate through the menu to access different modules:
   - Companies
   - Tours
   - Tasks
   - Work Entries
   - Money Entries
   - Reports

3. Each module provides:
   - Overview of entries
   - Add new entries
   - Edit existing entries
   - Delete entries
   - Export data
   - Generate reports

## Security Features

- Password hashing using bcrypt
- CSRF protection
- Input validation and sanitization
- Session management
- Role-based access control
- Activity logging

## Development

1. Follow PSR-4 autoloading standards
2. Use prepared statements for database queries
3. Implement proper error handling
4. Add unit tests for new features
5. Document code changes

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact the system administrator or create an issue in the repository. 