#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/billing-pages"
DB_USER="your_db_user"
DB_PASS="your_db_password"
DB_NAME="billing_pages"
SITE_DIR="/var/www/billing-pages"
DATE=$(date +%Y-%m-%d_%H-%M-%S)

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz $SITE_DIR

# Remove backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

# Log backup
echo "Backup completed at $DATE" >> $BACKUP_DIR/backup.log 