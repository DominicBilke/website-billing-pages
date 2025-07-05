# SCSS Circular Import Fix Guide

## Issue Description
The build was failing with the error:
```
Error: This file is already being loaded.
  ╷
1 │ @import "@/assets/styles/main.scss";
  │         ^^^^^^^^^^^^^^^^^^^
  ╵
  src/assets/styles/main.scss 1:9  root stylesheet
```

## Root Cause
The `main.scss` file was trying to import itself, creating a circular dependency.

## Solution Applied

### 1. Fixed Vite Configuration
Removed the circular import from `vite.config.js`:
```javascript
// REMOVED this problematic section:
css: {
  preprocessorOptions: {
    scss: {
      additionalData: `@import "@/assets/styles/main.scss";`
    }
  }
}
```

### 2. Created Separate Variables File
Created `src/assets/styles/variables.scss` with CSS variables:
```scss
// CSS Variables for theming
:root {
  --color-primary: #2563eb;
  --color-primary-dark: #1d4ed8;
  // ... other variables
}
```

### 3. Updated Main SCSS
Modified `src/assets/styles/main.scss` to import variables:
```scss
@import './variables.scss';

// Reset and base styles
* {
  box-sizing: border-box;
  // ... rest of styles
}
```

## Files Modified
- `vite.config.js` - Removed circular import
- `src/assets/styles/variables.scss` - Created new variables file
- `src/assets/styles/main.scss` - Updated to import variables

## Testing the Fix

### On Ubuntu Server
```bash
# Navigate to project directory
cd /var/www/vhosts/billing-pages.com/httpdocs

# Install dependencies
npm install

# Test build
npm run build

# Check if dist folder was created
ls -la dist/
```

### Expected Output
- Build should complete without errors
- `dist/` folder should be created
- CSS and JS files should be generated in `dist/assets/`

## Deployment Script
Use the updated `deploy-ubuntu.sh` script which includes the SCSS fix:
```bash
chmod +x deploy-ubuntu.sh
./deploy-ubuntu.sh
```

## Verification
After deployment, check:
1. Site loads without errors: `https://billing-pages.com`
2. No console errors in browser
3. Styles are applied correctly
4. Build process completes successfully

## Troubleshooting
If issues persist:
1. Clear npm cache: `npm cache clean --force`
2. Delete node_modules: `rm -rf node_modules package-lock.json`
3. Reinstall: `npm install`
4. Rebuild: `npm run build` 