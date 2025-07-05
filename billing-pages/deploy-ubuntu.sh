#!/bin/bash

# Deployment script for billing-pages.com on Ubuntu 24.04
# This script handles the complete deployment including the SCSS fix

set -e

DOMAIN="billing-pages.com"
PROJECT_DIR="/var/www/vhosts/$DOMAIN/httpdocs"
BACKUP_DIR="/var/www/vhosts/$DOMAIN/backups"

echo "🚀 Starting deployment for $DOMAIN..."

# Create backup
echo "📦 Creating backup..."
sudo mkdir -p "$BACKUP_DIR"
sudo cp -r "$PROJECT_DIR" "$BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S)" 2>/dev/null || echo "No existing project to backup"

# Install Node.js if not present
if ! command -v node &> /dev/null; then
    echo "📦 Installing Node.js 18.x..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

# Navigate to project directory
cd "$PROJECT_DIR"

# Install dependencies
echo "📦 Installing dependencies..."
npm install

# Fix SCSS circular import issue
echo "🔧 Fixing SCSS circular import issue..."
if [ -f "src/assets/styles/main.scss" ]; then
    # Ensure variables file exists with correct structure
    cat > src/assets/styles/variables.scss << 'EOF'
// CSS Variables for theming
:root {
  // Light theme colors
  --color-primary: #2563eb;
  --color-primary-dark: #1d4ed8;
  --color-primary-light: rgba(37, 99, 235, 0.1);
  
  --color-success: #10b981;
  --color-success-light: rgba(16, 185, 129, 0.1);
  
  --color-warning: #f59e0b;
  --color-warning-light: rgba(245, 158, 11, 0.1);
  
  --color-danger: #ef4444;
  --color-danger-light: rgba(239, 68, 68, 0.1);
  
  --color-background: #ffffff;
  --color-background-secondary: #f8fafc;
  --color-background-tertiary: #f1f5f9;
  
  --color-text-primary: #1e293b;
  --color-text-secondary: #64748b;
  --color-text-muted: #94a3b8;
  
  --color-border: #e2e8f0;
  --color-border-light: #f1f5f9;
  
  --color-shadow: rgba(0, 0, 0, 0.1);
  --color-shadow-dark: rgba(0, 0, 0, 0.2);
  
  // Spacing
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 2rem;
  --spacing-2xl: 3rem;
  
  // Border radius
  --radius-sm: 0.25rem;
  --radius-md: 0.5rem;
  --radius-lg: 0.75rem;
  --radius-xl: 1rem;
  
  // Typography
  --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --font-size-xs: 0.75rem;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  --font-size-2xl: 1.5rem;
  --font-size-3xl: 1.875rem;
  --font-size-4xl: 2.25rem;
  
  // Layout
  --max-width: 1400px;
  --sidebar-width: 280px;
  --header-height: 64px;
  
  // Transitions
  --transition-fast: 0.15s ease;
  --transition-normal: 0.3s ease;
  --transition-slow: 0.5s ease;
}

// Dark theme
[data-theme="dark"] {
  --color-background: #0f172a;
  --color-background-secondary: #1e293b;
  --color-background-tertiary: #334155;
  
  --color-text-primary: #f8fafc;
  --color-text-secondary: #cbd5e1;
  --color-text-muted: #64748b;
  
  --color-border: #334155;
  --color-border-light: #475569;
  
  --color-shadow: rgba(0, 0, 0, 0.3);
  --color-shadow-dark: rgba(0, 0, 0, 0.5);
}
EOF
    echo "✅ Variables file created/updated"
fi

# Build the project
echo "🔨 Building project..."
npm run build

# Check build success
if [ ! -d "dist" ]; then
    echo "❌ Build failed! Dist folder not created."
    exit 1
fi

echo "✅ Build completed successfully!"

# Set proper permissions
echo "🔐 Setting permissions..."
sudo chown -R www-data:www-data "$PROJECT_DIR"
sudo chmod -R 755 "$PROJECT_DIR"

# Restart Nginx
echo "🔄 Restarting Nginx..."
sudo systemctl restart nginx

# Test the deployment
echo "🧪 Testing deployment..."
if curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN" | grep -q "200"; then
    echo "✅ Deployment successful! Site is accessible."
else
    echo "⚠️  Site may not be accessible yet. Please check Nginx configuration."
fi

echo "🎉 Deployment completed for $DOMAIN!"
echo "📝 Next steps:"
echo "   - Verify the site is working at https://$DOMAIN"
echo "   - Check browser console for any JavaScript errors"
echo "   - Monitor logs: sudo tail -f /var/log/nginx/error.log" 