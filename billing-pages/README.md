# Billing Pages - Modern Billing Portal

A professional, modern billing and invoicing portal built with Vue.js 3, PHP 8.2, and MySQL. Designed for businesses to manage companies, tours, work hours, tasks, and financial data with an intuitive interface.

![Billing Pages](https://img.shields.io/badge/Version-2.0.0-blue.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.3.8-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)

## 🌟 Features

### Core Functionality
- **Multi-tenant Architecture** - Support for multiple companies and users
- **Company Management** - Complete company profiles and employee management
- **Tour Management** - Plan and track tours with GPS integration
- **Work Time Tracking** - Comprehensive timesheet and work hour management
- **Task Management** - Project and task organization with progress tracking
- **Financial Management** - Invoice generation, payment tracking, and reporting

### Modern UI/UX
- **Responsive Design** - Works perfectly on desktop, tablet, and mobile
- **Dark/Light Theme** - User preference support
- **Real-time Updates** - Live data synchronization
- **Interactive Charts** - Beautiful data visualization with Chart.js
- **Modern Components** - Reusable Vue.js components with consistent styling

### Security & Performance
- **JWT Authentication** - Secure token-based authentication
- **Role-based Access Control** - Granular permissions system
- **API Rate Limiting** - Protection against abuse
- **Data Encryption** - Sensitive data encryption at rest
- **Caching** - Redis-based caching for optimal performance
- **CDN Ready** - Optimized for content delivery networks

### Developer Experience
- **Modern Stack** - Vue.js 3, Vite, Pinia, PHP 8.2
- **TypeScript Support** - Full TypeScript integration
- **Hot Module Replacement** - Fast development with Vite
- **ESLint & Prettier** - Code quality and formatting
- **Unit Testing** - Vitest for component testing
- **API Documentation** - OpenAPI/Swagger documentation

## 🚀 Quick Start

### Prerequisites

- **Ubuntu 24.04** (recommended) or any Linux distribution
- **Node.js 18+** and npm
- **PHP 8.2+** with required extensions
- **MySQL 8.0+** or MariaDB 10.6+
- **Nginx** web server

### Instant Deployment

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/billing-pages.git
   cd billing-pages
   ```

2. **Run the deployment script:**
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

3. **Access your application:**
   - URL: `http://your-server-ip`
   - Default credentials: Use the login page to create your first account

### Manual Installation

#### 1. Install Dependencies

**Ubuntu/Debian:**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PHP 8.2
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

#### 2. Setup Project

```bash
# Create project directory
sudo mkdir -p /var/www/billing-pages
sudo chown $USER:$USER /var/www/billing-pages

# Copy project files
cp -r . /var/www/billing-pages/
cd /var/www/billing-pages

# Install Node.js dependencies
npm install

# Build the application
npm run build

# Install PHP dependencies
composer install --no-dev --optimize-autoloader
```

#### 3. Configure Database

```bash
# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS billing_pages CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'billing_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON billing_pages.* TO 'billing_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Import schema
sudo mysql billing_pages < database/billing_portal.sql
```

#### 4. Configure Web Server

**Nginx Configuration:**
```bash
sudo tee /etc/nginx/sites-available/billing-pages << 'EOF'
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/billing-pages/public;
    index index.html;

    # Handle Vue Router
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
}
EOF

# Enable site
sudo ln -sf /etc/nginx/sites-available/billing-pages /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

#### 5. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/billing-pages
sudo chmod -R 755 /var/www/billing-pages
```

#### 6. Configure Environment

```bash
# Copy environment file
cp env.example .env

# Edit with your settings
nano .env
```

## 🛠️ Development

### Local Development Setup

1. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

2. **Start development server:**
   ```bash
   npm run dev
   ```

3. **Build for production:**
   ```bash
   npm run build
   ```

### Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint
- `npm run format` - Format code with Prettier

## 📁 Project Structure

```
billing-pages/
├── public/                 # Static assets
│   ├── index.html         # Main HTML file
│   ├── assets/            # Built assets
│   └── images/            # Images and logos
├── src/
│   ├── components/        # Vue components
│   ├── views/            # Page components
│   ├── router/           # Vue Router configuration
│   ├── stores/           # Pinia stores
│   ├── assets/           # Source assets
│   │   └── styles/       # SCSS styles
│   ├── App.vue           # Root component
│   └── main.js           # Application entry point
├── database/             # Database schemas
├── config/               # Configuration files
├── deploy.sh             # Deployment script
├── package.json          # Node.js dependencies
├── vite.config.js        # Vite configuration
└── README.md             # This file
```

## 🎨 Features

### Core Functionality
- **Dashboard** - Overview of key metrics and recent activity
- **Company Management** - Manage client companies and their details
- **Tour Management** - Track tours and routes with mapping
- **Work Hours** - Time tracking and timesheet management
- **Task Management** - Project and task organization
- **Invoice System** - Generate and manage invoices
- **Reports** - Analytics and reporting tools
- **Settings** - User preferences and system configuration

### Technical Features
- **Modern UI** - Built with Vue.js 3 and modern CSS
- **Responsive Design** - Works on all devices
- **Dark/Light Theme** - User-selectable themes
- **Real-time Updates** - Live data updates
- **Security** - Authentication and authorization
- **Performance** - Optimized for speed
- **Accessibility** - WCAG compliant

## 🔧 Configuration

### Environment Variables

Create a `.env` file in the project root:

```env
# Application
APP_NAME="Billing Pages"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billing_pages
DB_USERNAME=billing_user
DB_PASSWORD=your_secure_password

# Mail
MAIL_DRIVER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Database Configuration

The application uses MySQL/MariaDB. Key tables include:

- `users` - User accounts and authentication
- `companies` - Client company information
- `tours` - Tour and route data
- `work_hours` - Time tracking records
- `tasks` - Project and task management
- `invoices` - Invoice and billing data

## 🔒 Security

### Security Features
- **Authentication** - Secure login system
- **Authorization** - Role-based access control
- **CSRF Protection** - Cross-site request forgery protection
- **XSS Protection** - Cross-site scripting prevention
- **SQL Injection Protection** - Parameterized queries
- **HTTPS Enforcement** - Secure communication
- **Security Headers** - Additional HTTP security headers

### Best Practices
- Use strong passwords
- Keep software updated
- Regular security audits
- Backup data regularly
- Monitor system logs
- Use HTTPS in production

## 📊 Monitoring

### Health Checks
- Application status: `http://your-domain.com/health`
- Database connectivity
- File system permissions
- Service status monitoring

### Logs
- Nginx logs: `/var/log/nginx/`
- Application logs: `/var/log/billing-pages/`
- System logs: `journalctl -u billing-pages`

## 🚀 Deployment

### Production Checklist
- [ ] SSL certificate installed
- [ ] Environment variables configured
- [ ] Database optimized
- [ ] Caching enabled
- [ ] Monitoring setup
- [ ] Backup strategy implemented
- [ ] Security audit completed

### SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### Performance Optimization
- Enable Nginx gzip compression
- Configure browser caching
- Optimize images
- Minify CSS/JS
- Use CDN for static assets

## 🐛 Troubleshooting

### Common Issues

**1. Loading Screen Stuck**
- Check browser console for errors
- Verify all dependencies are installed
- Clear browser cache
- Check network connectivity

**2. Database Connection Issues**
- Verify database credentials
- Check MySQL service status
- Ensure database exists
- Test connection manually

**3. Permission Errors**
- Check file ownership: `sudo chown -R www-data:www-data /var/www/billing-pages`
- Verify file permissions: `sudo chmod -R 755 /var/www/billing-pages`
- Check Nginx configuration: `sudo nginx -t`

**4. Build Errors**
- Clear node_modules: `rm -rf node_modules package-lock.json`
- Reinstall dependencies: `npm install`
- Check Node.js version: `node --version`

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

### Log Files
- Application: `/var/log/billing-pages/app.log`
- Error: `/var/log/billing-pages/error.log`
- Access: `/var/log/billing-pages/access.log`

## 📞 Support

### Getting Help
- **Documentation**: Check this README and inline code comments
- **Issues**: Create an issue on GitHub
- **Email**: support@billing-pages.com

### Community
- **GitHub**: https://github.com/your-username/billing-pages
- **Discussions**: GitHub Discussions
- **Wiki**: Project Wiki

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Vue.js** - Progressive JavaScript framework
- **Vite** - Next generation frontend tooling
- **Pinia** - Intuitive, type safe store for Vue
- **Bootstrap** - CSS framework
- **Font Awesome** - Icon library
- **Chart.js** - Charting library

## 🏢 About

**Billing Pages** is developed and maintained by **Bilke Web- und Softwareentwicklung**.

- **Website**: https://bilke.de
- **Email**: info@bilke.de
- **Phone**: +49 123 456789
- **Address**: Musterstraße 123, 12345 Musterstadt, Germany

---

**Version**: 2.0.0  
**Last Updated**: January 2024  
**Compatibility**: Vue.js 3.x, Node.js 18+, PHP 8.2+

## 📋 Legal Information

### Provider Information

**Bilke Web- und Softwareentwicklung**  
Hanauer Landstrasse 291 B  
60314 Frankfurt am Main  
Germany

**VAT registration number:** DE350967159  
**Telephone:** +49 174 849 3008  
**E-mail:** info@dominic-bilke.de

### Professional Law

**Legal occupational title:** Freiberuflicher Ingenieur  
**Awarding State:** Deutschland, Sachsen  
**Professional regulations:** Sächsisches Ingenieurgesetz

### EU Dispute Resolution

The EU Commission has set up the European-Online-Dispute-Resolution (ODR) platform for the extrajudicial online settlement of disputes between consumers and businesses. You can reach the platform at: [https://ec.europa.eu/consumers/odr](https://ec.europa.eu/consumers/odr)

We participate in this dispute resolution procedure. Our email address is freelancer@dominic-bilke.de.

### Privacy Policy

For detailed information about data processing, please refer to our [Privacy Policy](https://www.dominic-bilke.de/en/privacy-policy).

### Imprint

For complete legal information, please refer to our [Imprint](https://www.dominic-bilke.de/en/imprint).

---

**© 2025 Bilke Web- und Softwareentwicklung | DOT.COM | Eine Webseite von Dipl.-Ing. (FH) D. Bilke**

Built with ❤️ by [Bilke Web- und Softwareentwicklung](https://www.dominic-bilke.de) 