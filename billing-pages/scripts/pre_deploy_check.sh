#!/bin/bash

# Exit on error
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Log function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

# Check system requirements
check_system() {
    log "Checking system requirements..."
    
    # Check PHP version
    PHP_VERSION=$(php -v | grep -oP '(?<=PHP )[0-9]+\.[0-9]+')
    if (( $(echo "$PHP_VERSION < 8.1" | bc -l) )); then
        error "PHP version must be 8.1 or higher"
    fi
    
    # Check MySQL version
    MYSQL_VERSION=$(mysql --version | grep -oP '(?<=Ver )[0-9]+\.[0-9]+')
    if (( $(echo "$MYSQL_VERSION < 5.7" | bc -l) )); then
        error "MySQL version must be 5.7 or higher"
    fi
    
    # Check Apache version
    APACHE_VERSION=$(apache2 -v | grep -oP '(?<=Apache/)[0-9]+\.[0-9]+')
    if (( $(echo "$APACHE_VERSION < 2.4" | bc -l) )); then
        error "Apache version must be 2.4 or higher"
    fi
    
    # Check disk space
    DISK_SPACE=$(df -BG / | awk 'NR==2 {print $4}' | sed 's/G//')
    if (( $(echo "$DISK_SPACE < 10" | bc -l) )); then
        warning "Less than 10GB of disk space available"
    fi
    
    # Check memory
    MEMORY=$(free -g | awk '/^Mem:/{print $2}')
    if (( $(echo "$MEMORY < 2" | bc -l) )); then
        warning "Less than 2GB of RAM available"
    fi
    
    log "System requirements check completed"
}

# Check required PHP extensions
check_php_extensions() {
    log "Checking PHP extensions..."
    
    REQUIRED_EXTENSIONS=(
        "mysqli"
        "pdo_mysql"
        "curl"
        "gd"
        "mbstring"
        "xml"
        "zip"
        "intl"
        "bcmath"
        "gmp"
        "ldap"
        "imap"
        "soap"
        "xmlrpc"
        "json"
        "opcache"
    )
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if ! php -m | grep -q "^$ext$"; then
            error "PHP extension $ext is not installed"
        fi
    done
    
    log "PHP extensions check completed"
}

# Check file permissions
check_permissions() {
    log "Checking file permissions..."
    
    DIRECTORIES=(
        "/var/www/billing-pages"
        "/var/www/billing-pages/uploads"
        "/var/www/billing-pages/logs"
        "/var/www/billing-pages/temp"
    )
    
    for dir in "${DIRECTORIES[@]}"; do
        if [ -d "$dir" ]; then
            if [ "$(stat -c %U $dir)" != "www-data" ]; then
                warning "Directory $dir is not owned by www-data"
            fi
            if [ "$(stat -c %a $dir)" != "755" ]; then
                warning "Directory $dir has incorrect permissions"
            fi
        fi
    done
    
    log "File permissions check completed"
}

# Check database connection
check_database() {
    log "Checking database connection..."
    
    if ! mysql -u $DB_USER -p$DB_PASS -e "SELECT 1" > /dev/null 2>&1; then
        error "Cannot connect to database"
    fi
    
    log "Database connection check completed"
}

# Check SSL certificate
check_ssl() {
    log "Checking SSL certificate..."
    
    if ! openssl s_client -connect $DOMAIN:443 -servername $DOMAIN </dev/null 2>/dev/null | grep -q "BEGIN CERTIFICATE"; then
        warning "SSL certificate not found or invalid"
    fi
    
    log "SSL certificate check completed"
}

# Main check process
main() {
    log "Starting pre-deployment checks..."
    
    check_system
    check_php_extensions
    check_permissions
    check_database
    check_ssl
    
    log "All pre-deployment checks completed successfully!"
}

# Run main function
main 