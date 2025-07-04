# Troubleshooting Guide

This guide helps you resolve common issues when deploying and running Billing Pages.

## 🚨 Loading Screen Issues

### Problem: Application shows loading screen indefinitely

**Symptoms:**
- Loading spinner appears and never disappears
- Browser console shows errors
- Network tab shows failed requests

**Solutions:**

1. **Check Browser Console**
   ```bash
   # Open browser developer tools (F12)
   # Look for JavaScript errors in Console tab
   ```

2. **Verify Dependencies**
   ```bash
   # Reinstall Node.js dependencies
   rm -rf node_modules package-lock.json
   npm install
   ```

3. **Check Build Process**
   ```bash
   # Build the application
   npm run build
   
   # Check if dist folder was created
   ls -la public/dist/
   ```

4. **Verify Router Configuration**
   - Check `src/router/index.js` for correct route paths
   - Ensure all view components exist
   - Verify import statements

5. **Check Authentication Store**
   - Verify `src/stores/auth.js` exists
   - Check if localStorage is accessible
   - Ensure auth check completes

## 🗄️ Database Issues

### Problem: Database connection fails

**Symptoms:**
- "Database connection failed" errors
- Application cannot load data
- MySQL service not running

**Solutions:**

1. **Check MySQL Service**
   ```bash
   sudo systemctl status mysql
   sudo systemctl start mysql
   sudo systemctl enable mysql
   ```

2. **Verify Database Credentials**
   ```bash
   # Test connection manually
   mysql -u billing_user -p billing_pages
   ```

3. **Check Database Exists**
   ```bash
   sudo mysql -e "SHOW DATABASES;"
   sudo mysql -e "USE billing_pages; SHOW TABLES;"
   ```

4. **Reset Database**
   ```bash
   # Drop and recreate database
   sudo mysql -e "DROP DATABASE IF EXISTS billing_pages;"
   sudo mysql -e "CREATE DATABASE billing_pages CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   sudo mysql billing_pages < database/billing_portal.sql
   ```

## 🌐 Web Server Issues

### Problem: Nginx not serving the application

**Symptoms:**
- 404 errors
- Blank pages
- Wrong content served

**Solutions:**

1. **Check Nginx Status**
   ```bash
   sudo systemctl status nginx
   sudo nginx -t
   ```

2. **Verify Configuration**
   ```bash
   # Check site configuration
   sudo cat /etc/nginx/sites-available/billing-pages
   
   # Test configuration
   sudo nginx -t
   ```

3. **Check File Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/billing-pages
   sudo chmod -R 755 /var/www/billing-pages
   ```

4. **Check Logs**
   ```bash
   sudo tail -f /var/log/nginx/error.log
   sudo tail -f /var/log/nginx/access.log
   ```

## 🔐 Authentication Issues

### Problem: Cannot log in or stay logged in

**Symptoms:**
- Login form doesn't work
- Redirected to login after authentication
- Session not maintained

**Solutions:**

1. **Clear Browser Data**
   - Clear cookies and localStorage
   - Try incognito/private mode
   - Test in different browser

2. **Check Auth Store**
   ```javascript
   // In browser console
   localStorage.getItem('auth_token')
   localStorage.removeItem('auth_token')
   ```

3. **Verify Router Guards**
   - Check `src/router/index.js` navigation guards
   - Ensure auth check logic is correct

4. **Check API Endpoints**
   - Verify backend API is running
   - Check network requests in browser

## 📱 Responsive Issues

### Problem: Layout breaks on mobile devices

**Symptoms:**
- Elements overlap on small screens
- Navigation doesn't work on mobile
- Text too small to read

**Solutions:**

1. **Check Viewport Meta Tag**
   ```html
   <!-- In public/index.html -->
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   ```

2. **Test Responsive Design**
   - Use browser dev tools device simulation
   - Test on actual mobile devices
   - Check CSS media queries

3. **Fix CSS Issues**
   ```scss
   // Add responsive utilities
   @media (max-width: 768px) {
     .container {
       padding: 0 1rem;
     }
   }
   ```

## 🎨 Theme Issues

### Problem: Dark/light theme not working

**Symptoms:**
- Theme toggle doesn't work
- Theme not persisted
- Inconsistent styling

**Solutions:**

1. **Check Theme Store**
   ```javascript
   // In browser console
   localStorage.getItem('theme')
   document.documentElement.getAttribute('data-theme')
   ```

2. **Verify CSS Variables**
   ```css
   /* Check if CSS variables are defined */
   :root {
     --color-primary: #2563eb;
   }
   
   [data-theme="dark"] {
     --color-background: #0f172a;
   }
   ```

3. **Check Theme Toggle**
   - Verify theme store is imported
   - Check toggle function works
   - Ensure DOM updates correctly

## 🔧 Build Issues

### Problem: Application won't build

**Symptoms:**
- Build process fails
- Missing dependencies
- Compilation errors

**Solutions:**

1. **Check Node.js Version**
   ```bash
   node --version  # Should be 18+
   npm --version
   ```

2. **Clear Cache**
   ```bash
   npm cache clean --force
   rm -rf node_modules package-lock.json
   npm install
   ```

3. **Check Dependencies**
   ```bash
   # Update dependencies
   npm update
   
   # Check for vulnerabilities
   npm audit
   npm audit fix
   ```

4. **Verify Vite Config**
   ```javascript
   // Check vite.config.js
   // Ensure all paths are correct
   // Verify plugin configuration
   ```

## 🚀 Performance Issues

### Problem: Application is slow

**Symptoms:**
- Long loading times
- Slow interactions
- High memory usage

**Solutions:**

1. **Enable Production Build**
   ```bash
   npm run build
   # Serve from public/dist/ directory
   ```

2. **Optimize Images**
   ```bash
   # Compress images
   # Use WebP format
   # Implement lazy loading
   ```

3. **Enable Caching**
   ```nginx
   # In Nginx configuration
   location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
     expires 1y;
     add_header Cache-Control "public, immutable";
   }
   ```

4. **Check Bundle Size**
   ```bash
   npm run build
   # Check dist/assets/ folder size
   ```

## 🔍 Debug Mode

### Enable Debug Mode

1. **Set Environment Variable**
   ```env
   APP_DEBUG=true
   ```

2. **Enable Vue DevTools**
   - Install Vue DevTools browser extension
   - Enable in development mode

3. **Check Network Tab**
   - Open browser dev tools
   - Monitor network requests
   - Check for failed requests

4. **Console Logging**
   ```javascript
   // Add debug logs
   console.log('Debug info:', data);
   console.error('Error:', error);
   ```

## 📞 Getting Help

### Before Asking for Help

1. **Check this guide** for your specific issue
2. **Search existing issues** on GitHub
3. **Check browser console** for errors
4. **Verify system requirements** are met
5. **Test with minimal configuration**

### When Reporting Issues

Include the following information:

1. **Environment Details**
   - Operating system and version
   - Node.js version
   - Browser and version
   - Error messages

2. **Steps to Reproduce**
   - Exact steps to trigger the issue
   - Expected vs actual behavior
   - Screenshots if applicable

3. **Technical Details**
   - Browser console errors
   - Network request failures
   - System logs
   - Configuration files

### Support Channels

- **GitHub Issues**: Create an issue with detailed information
- **Documentation**: Check README.md and inline comments
- **Community**: Ask in GitHub Discussions
- **Email**: support@billing-pages.com

## 🔄 Common Fixes

### Quick Fixes

1. **Restart Services**
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart mysql
   ```

2. **Clear Caches**
   ```bash
   # Browser cache
   # Application cache
   # Nginx cache
   sudo rm -rf /var/cache/nginx/*
   ```

3. **Reset Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/billing-pages
   sudo chmod -R 755 /var/www/billing-pages
   ```

4. **Reinstall Dependencies**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   composer install
   ```

---

**Remember**: Most issues can be resolved by checking logs, verifying configuration, and ensuring all dependencies are properly installed. 