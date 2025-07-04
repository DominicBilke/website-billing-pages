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

- Node.js 18+ 
- PHP 8.2+
- MySQL 8.0+
- Composer
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/billing-pages.git
   cd billing-pages
   ```

2. **Install dependencies**
   ```bash
   # Frontend dependencies
   npm install
   
   # Backend dependencies
   composer install
   ```

3. **Environment setup**
   ```bash
   cp env.example .env
   # Edit .env with your database and API settings
   ```

4. **Database setup**
   ```bash
   # Run migrations
   php artisan migrate
   
   # Seed with sample data (optional)
   php artisan db:seed
   ```

5. **Build frontend**
   ```bash
   npm run build
   ```

6. **Start development servers**
   ```bash
   # Frontend (Vite dev server)
   npm run dev
   
   # Backend (PHP development server)
   php artisan serve
   ```

7. **Access the application**
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000

## 🏗️ Architecture

### Frontend (Vue.js 3)
```
src/
├── components/          # Reusable Vue components
│   ├── ui/             # Base UI components
│   └── forms/          # Form components
├── views/              # Page components
├── stores/             # Pinia state management
├── router/             # Vue Router configuration
├── utils/              # Utility functions
├── assets/             # Static assets
│   ├── styles/         # SCSS stylesheets
│   └── images/         # Images and icons
└── main.js             # Application entry point
```

### Backend (PHP/Laravel)
```
app/
├── Http/
│   ├── Controllers/    # API controllers
│   ├── Middleware/     # Custom middleware
│   └── Requests/       # Form request validation
├── Models/             # Eloquent models
├── Services/           # Business logic services
├── Repositories/       # Data access layer
└── Providers/          # Service providers
```

## 📁 Project Structure

```
billing-pages/
├── src/                    # Vue.js frontend source
│   ├── components/         # Vue components
│   ├── views/             # Page views
│   ├── stores/            # Pinia stores
│   ├── router/            # Vue Router
│   ├── utils/             # Utilities
│   └── assets/            # Static assets
├── public/                # Public assets
│   ├── dist/              # Built frontend files
│   └── assets/            # Static files
├── app/                   # PHP backend
├── database/              # Database migrations & seeds
├── config/                # Configuration files
├── routes/                # API routes
├── storage/               # File storage
├── tests/                 # Test files
├── vendor/                # Composer dependencies
├── node_modules/          # NPM dependencies
├── package.json           # Frontend dependencies
├── composer.json          # Backend dependencies
├── vite.config.js         # Vite configuration
├── .env.example           # Environment template
└── README.md              # This file
```

## 🛠️ Development

### Available Scripts

```bash
# Frontend Development
npm run dev              # Start Vite dev server
npm run build            # Build for production
npm run preview          # Preview production build
npm run lint             # Run ESLint
npm run format           # Format code with Prettier
npm run test             # Run unit tests
npm run test:ui          # Run tests with UI

# Backend Development
composer install         # Install PHP dependencies
composer update          # Update PHP dependencies
composer test            # Run PHP tests
php artisan serve        # Start PHP development server
php artisan migrate      # Run database migrations
php artisan db:seed      # Seed database
```

### Code Style

This project uses:
- **ESLint** for JavaScript/TypeScript linting
- **Prettier** for code formatting
- **PHP CS Fixer** for PHP code formatting
- **PSR-12** for PHP coding standards

### Testing

```bash
# Frontend tests
npm run test             # Run all tests
npm run test:unit        # Run unit tests only
npm run test:coverage    # Generate coverage report

# Backend tests
composer test            # Run PHP tests
php artisan test         # Run Laravel tests
```

## 🚀 Deployment

### Ubuntu 24.04 with Plesk

1. **Run the deployment script**
   ```bash
   chmod +x deploy-ubuntu-plesk.sh
   ./deploy-ubuntu-plesk.sh
   ```

2. **Manual deployment steps**
   ```bash
   # Build frontend
   npm run build
   
   # Install production dependencies
   composer install --no-dev --optimize-autoloader
   
   # Run migrations
   php artisan migrate --force
   
   # Optimize application
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Docker Deployment

```bash
# Build and run with Docker Compose
docker-compose up -d

# Or build individual containers
docker build -t billing-pages-frontend ./frontend
docker build -t billing-pages-backend ./backend
```

### Environment Variables

```env
# Application
APP_NAME="Billing Pages"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://billing-pages.com
APP_TIMEZONE=Europe/Berlin
APP_LOCALE=de

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=billing_pages_db
DB_USERNAME=billing_pages_user
DB_PASSWORD=your_secure_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@billing-pages.com"
MAIL_FROM_NAME="${APP_NAME}"

# JWT Authentication
JWT_SECRET=your_jwt_secret_key_here
JWT_TTL=60
JWT_REFRESH_TTL=20160

# File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Queue
QUEUE_CONNECTION=redis
QUEUE_FAILED_DRIVER=database-uuids

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Frontend
VITE_APP_NAME="${APP_NAME}"
VITE_APP_URL="${APP_URL}"
VITE_API_URL="${APP_URL}/api"

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CORS_ALLOWED_ORIGINS="${APP_URL}"

# Monitoring
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=1.0

# External Services
GOOGLE_MAPS_API_KEY=
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
```

## 🔧 Configuration

### Frontend Configuration

The frontend uses Vite for building and development. Key configuration files:

- `vite.config.js` - Vite configuration
- `package.json` - Dependencies and scripts
- `src/main.js` - Application entry point
- `src/router/index.js` - Route configuration

### Backend Configuration

The backend uses Laravel framework. Key configuration files:

- `config/app.php` - Application configuration
- `config/database.php` - Database configuration
- `config/auth.php` - Authentication configuration
- `routes/api.php` - API routes

## 📊 API Documentation

### Authentication

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Companies

```http
GET /api/companies
Authorization: Bearer {token}

POST /api/companies
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Company Name",
  "industry": "Technology",
  "address": "123 Main St",
  "phone": "+1234567890",
  "email": "contact@company.com"
}
```

### Tours

```http
GET /api/tours
Authorization: Bearer {token}

POST /api/tours
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Tour Name",
  "destination": "Destination",
  "start_date": "2024-01-01",
  "end_date": "2024-01-07",
  "description": "Tour description"
}
```

## 🔒 Security

### Authentication & Authorization
- JWT-based authentication
- Role-based access control (RBAC)
- Permission-based authorization
- Session management
- Password hashing with bcrypt

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Rate limiting
- Data encryption at rest

### Security Headers
- Content Security Policy (CSP)
- X-Frame-Options
- X-Content-Type-Options
- X-XSS-Protection
- Referrer Policy

## 📈 Performance

### Frontend Optimization
- Code splitting and lazy loading
- Tree shaking for unused code
- Image optimization
- Gzip compression
- Browser caching
- CDN integration

### Backend Optimization
- Database query optimization
- Redis caching
- OpCache for PHP
- Queue system for background jobs
- Database indexing
- API response caching

## 🧪 Testing

### Frontend Testing
- Unit tests with Vitest
- Component testing with Vue Test Utils
- E2E testing with Playwright
- Visual regression testing

### Backend Testing
- Unit tests with PHPUnit
- Feature tests for API endpoints
- Database testing
- Performance testing

## 📝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow the existing code style
- Write tests for new features
- Update documentation
- Ensure all tests pass
- Follow semantic versioning

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Documentation
- [API Documentation](https://docs.billing-pages.com)
- [User Guide](https://guide.billing-pages.com)
- [Developer Guide](https://dev.billing-pages.com)

### Community
- [GitHub Issues](https://github.com/your-org/billing-pages/issues)
- [Discussions](https://github.com/your-org/billing-pages/discussions)
- [Discord Server](https://discord.gg/billing-pages)

### Professional Support
- Email: support@billing-pages.com
- Phone: +1 (555) 123-4567
- Business Hours: Monday - Friday, 9 AM - 6 PM EST

## 🙏 Acknowledgments

- [Vue.js](https://vuejs.org/) - Progressive JavaScript framework
- [Laravel](https://laravel.com/) - PHP web application framework
- [Vite](https://vitejs.dev/) - Next generation frontend tooling
- [Pinia](https://pinia.vuejs.org/) - Intuitive, type safe store for Vue
- [Chart.js](https://www.chartjs.org/) - Simple yet flexible JavaScript charting
- [Font Awesome](https://fontawesome.com/) - Icon toolkit

## 📊 Project Status

![GitHub last commit](https://img.shields.io/github/last-commit/your-org/billing-pages)
![GitHub issues](https://img.shields.io/github/issues/your-org/billing-pages)
![GitHub pull requests](https://img.shields.io/github/issues-pr/your-org/billing-pages)
![GitHub stars](https://img.shields.io/github/stars/your-org/billing-pages)

---

**Billing Pages** - Professional billing and invoicing portal for modern businesses.

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