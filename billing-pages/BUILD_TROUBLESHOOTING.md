# Build Troubleshooting Guide

This guide specifically addresses build-related issues with the Billing Pages application.

## 🚨 Common Build Errors

### Error: "Could not resolve entry module 'index.html'"

**Symptoms:**
```
RollupError: Could not resolve entry module "index.html".
```

**Root Cause:**
- Vite cannot find the entry point file
- Incorrect Vite configuration
- Missing or misplaced index.html file

**Solutions:**

1. **Check File Structure**
   ```bash
   # Ensure index.html is in the root directory
   ls -la index.html
   
   # If not present, copy from public directory
   cp public/index.html index.html
   ```

2. **Verify Vite Configuration**
   ```javascript
   // vite.config.js should have:
   export default defineConfig({
     plugins: [vue()],
     root: '.',
     publicDir: 'public',
     build: {
       outDir: 'dist',
       rollupOptions: {
         input: {
           main: resolve(__dirname, 'index.html')
         }
       }
     }
   })
   ```

3. **Clean and Rebuild**
   ```bash
   # Remove previous build artifacts
   rm -rf dist
   rm -rf node_modules/.vite
   
   # Reinstall dependencies
   rm -rf node_modules package-lock.json
   npm install
   
   # Try building again
   npm run build
   ```

### Error: "Module not found"

**Symptoms:**
```
Module not found: Can't resolve '@/components/SomeComponent'
```

**Solutions:**

1. **Check Import Paths**
   ```javascript
   // Use correct alias paths
   import Component from '@/components/Component.vue'
   import View from '@/views/View.vue'
   import Store from '@/stores/store.js'
   ```

2. **Verify Alias Configuration**
   ```javascript
   // vite.config.js
   resolve: {
     alias: {
       '@': resolve(__dirname, 'src'),
       '@components': resolve(__dirname, 'src/components'),
       '@views': resolve(__dirname, 'src/Views'),
       '@stores': resolve(__dirname, 'src/stores')
     }
   }
   ```

3. **Check File Existence**
   ```bash
   # Verify files exist
   ls -la src/components/
   ls -la src/Views/
   ls -la src/stores/
   ```

### Error: "SCSS/SASS compilation failed"

**Symptoms:**
```
Error: Can't resolve '@/assets/styles/variables.scss'
```

**Solutions:**

1. **Check SCSS File Path**
   ```bash
   # Verify the file exists
   ls -la src/assets/styles/
   ```

2. **Update Vite Config**
   ```javascript
   css: {
     preprocessorOptions: {
       scss: {
         additionalData: `@import "@/assets/styles/main.scss";`
       }
     }
   }
   ```

3. **Install SASS**
   ```bash
   npm install -D sass
   ```

## 🔧 Build Process

### Standard Build Commands

```bash
# Development
npm run dev

# Production build
npm run build

# Preview production build
npm run preview

# Clean build
rm -rf dist && npm run build
```

### Using the Build Script

```bash
# Make script executable
chmod +x build.sh

# Run build script
./build.sh
```

### Manual Build Steps

1. **Prerequisites**
   ```bash
   # Check Node.js version (18+ required)
   node --version
   
   # Check npm version
   npm --version
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Verify Entry Point**
   ```bash
   # Ensure index.html exists in root
   ls -la index.html
   ```

4. **Build Application**
   ```bash
   npm run build
   ```

5. **Verify Build Output**
   ```bash
   # Check dist directory
   ls -la dist/
   
   # Check main files
   ls -la dist/index.html
   ls -la dist/assets/
   ```

## 🐛 Debug Build Issues

### Enable Verbose Logging

```bash
# Run build with verbose output
npm run build -- --debug

# Or use Vite directly
npx vite build --debug
```

### Check Dependencies

```bash
# Check for outdated packages
npm outdated

# Check for security vulnerabilities
npm audit

# Fix vulnerabilities
npm audit fix
```

### Verify Configuration Files

1. **package.json**
   ```json
   {
     "scripts": {
       "build": "vite build"
     }
   }
   ```

2. **vite.config.js**
   ```javascript
   import { defineConfig } from 'vite'
   import vue from '@vitejs/plugin-vue'
   import { resolve } from 'path'

   export default defineConfig({
     plugins: [vue()],
     root: '.',
     publicDir: 'public',
     build: {
       outDir: 'dist'
     }
   })
   ```

3. **index.html**
   ```html
   <!DOCTYPE html>
   <html>
   <head>
     <title>Billing Pages</title>
   </head>
   <body>
     <div id="app"></div>
     <script type="module" src="/src/main.js"></script>
   </body>
   </html>
   ```

## 🚀 Production Build Optimization

### Optimize Bundle Size

```javascript
// vite.config.js
build: {
  rollupOptions: {
    output: {
      manualChunks: {
        vendor: ['vue', 'vue-router', 'pinia'],
        charts: ['chart.js', 'vue-chartjs'],
        maps: ['leaflet', 'vue-leaflet']
      }
    }
  }
}
```

### Enable Source Maps (Development)

```javascript
build: {
  sourcemap: true
}
```

### Disable Source Maps (Production)

```javascript
build: {
  sourcemap: false
}
```

## 📁 Expected File Structure

```
billing-pages/
├── index.html              # Entry point (MUST be in root)
├── package.json
├── vite.config.js
├── src/
│   ├── main.js
│   ├── App.vue
│   ├── components/
│   ├── Views/
│   ├── stores/
│   └── assets/
├── public/
│   └── assets/
└── dist/                   # Build output
    ├── index.html
    └── assets/
```

## 🔍 Common Issues and Solutions

### Issue: Build succeeds but app doesn't work

**Check:**
1. Browser console for JavaScript errors
2. Network tab for failed requests
3. File permissions on dist directory
4. Web server configuration

### Issue: Assets not loading

**Check:**
1. Asset paths in dist/index.html
2. Web server static file serving
3. File permissions
4. Base URL configuration

### Issue: Router not working

**Check:**
1. Web server rewrite rules
2. Vue Router configuration
3. Base URL in router
4. History mode vs hash mode

## 📞 Getting Help

### Before Reporting Issues

1. **Check this guide** for your specific error
2. **Try the build script**: `./build.sh`
3. **Check Node.js version**: `node --version`
4. **Verify file structure**: `ls -la`
5. **Check console output** for detailed errors

### When Reporting Build Issues

Include:
- **Error message** (exact text)
- **Node.js version**: `node --version`
- **npm version**: `npm --version`
- **Operating system**
- **Steps to reproduce**
- **Console output** (full error)

### Support Channels

- **GitHub Issues**: Create issue with build error details
- **Documentation**: Check README.md and this guide
- **Community**: Ask in GitHub Discussions

---

**Remember**: Most build issues can be resolved by ensuring the correct file structure and configuration. 