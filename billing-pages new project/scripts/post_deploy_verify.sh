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

# Verify web server
verify_webserver() {
    log "Verifying web server..."
    
    # Check if Apache is running
    if ! systemctl is-active --quiet apache2; then
        error "Apache is not running"
    fi
    
    # Check if site is accessible
    if ! curl -s -f "https://$DOMAIN" > /dev/null; then
        error "Website is not accessible"
    fi
    
    # Check SSL
    if ! curl -s -f "https://$DOMAIN" > /dev/null; then
        error "SSL is not working properly"
    fi
    
    log "Web server verification completed"
}

# Verify database
verify_database() {
    log "Verifying database..."
    
    # Check database connection
    if ! mysql -u $DB_USER -p$DB_PASS -e "SELECT 1" > /dev/null 2>&1; then
        error "Cannot connect to database"
    fi
    
    # Check required tables
    REQUIRED_TABLES=(
        "users"
        "companies"
        "tours"
        "tasks"
        "work"
        "money"
    )
    
    for table in "${REQUIRED_TABLES[@]}"; do
        if ! mysql -u $DB_USER -p$DB_PASS $DB_NAME -e "SELECT 1 FROM $table LIMIT 1" > /dev/null 2>&1; then
            error "Table $table is missing or inaccessible"
        fi
    done
    
    log "Database verification completed"
}

# Verify file permissions
verify_permissions() {
    log "Verifying file permissions..."
    
    DIRECTORIES=(
        "/var/www/billing-pages"
        "/var/www/billing-pages/uploads"
        "/var/www/billing-pages/logs"
        "/var/www/billing-pages/temp"
    )
    
    for dir in "${DIRECTORIES[@]}"; do
        if [ ! -d "$dir" ]; then
            error "Directory $dir does not exist"
        fi
        
        if [ "$(stat -c %U $dir)" != "www-data" ]; then
            error "Directory $dir is not owned by www-data"
        fi
        
        if [ "$(stat -c %a $dir)" != "755" ]; then
            error "Directory $dir has incorrect permissions"
        fi
    done
    
    log "File permissions verification completed"
}

# Verify application functionality
verify_functionality() {
    log "Verifying application functionality..."
    
    # Test login
    if ! curl -s -f -X POST "https://$DOMAIN/login.php" \
        -d "username=test&password=test" \
        -c cookies.txt > /dev/null; then
        warning "Login functionality might be broken"
    fi
    
    # Test file upload
    if ! curl -s -f -X POST "https://$DOMAIN/upload.php" \
        -F "file=@test.txt" \
        -b cookies.txt > /dev/null; then
        warning "File upload functionality might be broken"
    fi
    
    # Test database operations
    if ! curl -s -f "https://$DOMAIN/api/test" > /dev/null; then
        warning "API functionality might be broken"
    fi
    
    log "Application functionality verification completed"
}

# Verify security
verify_security() {
    log "Verifying security measures..."
    
    # Check fail2ban
    if ! systemctl is-active --quiet fail2ban; then
        error "fail2ban is not running"
    fi
    
    # Check SSL configuration
    if ! curl -s -f "https://$DOMAIN" > /dev/null; then
        error "SSL is not properly configured"
    fi
    
    # Check file permissions
    if [ -f "/var/www/billing-pages/.env" ]; then
        if [ "$(stat -c %a /var/www/billing-pages/.env)" != "600" ]; then
            error ".env file has incorrect permissions"
        fi
    fi
    
    log "Security verification completed"
}

# Verify monitoring
verify_monitoring() {
    log "Verifying monitoring setup..."
    
    # Check logwatch
    if ! command -v logwatch &> /dev/null; then
        warning "logwatch is not installed"
    fi
    
    # Check error logs
    if [ -f "/var/log/apache2/billing-pages-error.log" ]; then
        if [ -s "/var/log/apache2/billing-pages-error.log" ]; then
            warning "Error log contains entries"
        fi
    fi
    
    log "Monitoring verification completed"
}

# Main verification process
main() {
    log "Starting post-deployment verification..."
    
    verify_webserver
    verify_database
    verify_permissions
    verify_functionality
    verify_security
    verify_monitoring
    
    log "All post-deployment verifications completed successfully!"
}

# Run main function
main 