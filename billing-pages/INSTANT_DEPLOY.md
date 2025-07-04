# 🚀 Instant Deployment Guide

## Quick Start (5 minutes)

### Prerequisites
- Ubuntu 24.04 server
- User with sudo privileges
- Internet connection

### 1. Clone and Deploy

```bash
# Clone the repository
git clone https://github.com/your-org/billing-pages.git
cd billing-pages

# Run the quick deployment script
./quick-deploy.sh
```

### 2. Access Your Application

- **URL**: http://localhost (or your server IP)
- **Default Admin**: admin@billing-pages.com / password
- **Database**: billing_pages_db

## Manual Deployment

If you prefer manual deployment:

### 1. Install Dependencies

```bash
# System packages
sudo apt update
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql nodejs composer

# Application dependencies
npm install
composer install --no-dev
```

### 2. Build Application

```bash
npm run build
```

### 3. Configure Database

```bash
sudo mysql -e "CREATE DATABASE billing_pages_db;"
sudo mysql -e "CREATE USER 'billing_user'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON billing_pages_db.* TO 'billing_user'@'localhost';"
```

### 4. Configure Nginx

```bash
sudo cp nginx.conf /etc/nginx/sites-available/billing-pages
sudo ln -s /etc/nginx/sites-available/billing-pages /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

## Production Deployment

### 1. Update Environment

Edit `.env` file:
```env
APP_URL=https://your-domain.com
DB_PASSWORD=your_secure_password
```

### 2. Install SSL

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 3. Configure Firewall

```bash
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

## Troubleshooting

### Common Issues

1. **Permission Denied**
   ```bash
   sudo chown -R www-data:www-data /var/www/vhosts/billing-pages.com
   ```

2. **Database Connection Error**
   ```bash
   sudo mysql -e "FLUSH PRIVILEGES;"
   ```

3. **Nginx Configuration Error**
   ```bash
   sudo nginx -t
   sudo systemctl reload nginx
   ```

### Logs

- **Nginx**: `/var/log/nginx/error.log`
- **PHP**: `/var/log/php8.2-fpm.log`
- **Application**: `/var/www/vhosts/billing-pages.com/storage/logs/`

## Support

- **Documentation**: [docs.billing-pages.com](https://docs.billing-pages.com)
- **Issues**: [GitHub Issues](https://github.com/your-org/billing-pages/issues)
- **Email**: support@billing-pages.com

---

**Billing Pages** - Professional billing portal for modern businesses. 