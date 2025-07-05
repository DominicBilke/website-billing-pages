#!/usr/bin/env node

// Billing Pages - Build Verification Script
// This script verifies that all required files are in place for a successful build

const fs = require('fs');
const path = require('path');

console.log('🔍 Verifying Billing Pages build setup...\n');

const requiredFiles = [
  'index.html',
  'package.json',
  'vite.config.js',
  'src/main.js',
  'src/App.vue',
  'src/router/index.js',
  'src/stores/auth.js',
  'src/stores/theme.js',
  'src/assets/styles/main.scss'
];

const requiredDirs = [
  'src',
  'src/components',
  'src/Views',
  'src/stores',
  'src/router',
  'src/assets',
  'src/assets/styles',
  'public',
  'public/assets'
];

let allGood = true;

// Check required files
console.log('📄 Checking required files:');
requiredFiles.forEach(file => {
  if (fs.existsSync(file)) {
    console.log(`   ✅ ${file}`);
  } else {
    console.log(`   ❌ ${file} - MISSING`);
    allGood = false;
  }
});

console.log('\n📁 Checking required directories:');
requiredDirs.forEach(dir => {
  if (fs.existsSync(dir) && fs.statSync(dir).isDirectory()) {
    console.log(`   ✅ ${dir}/`);
  } else {
    console.log(`   ❌ ${dir}/ - MISSING`);
    allGood = false;
  }
});

// Check package.json scripts
console.log('\n📦 Checking package.json:');
try {
  const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
  
  if (packageJson.scripts && packageJson.scripts.build) {
    console.log('   ✅ build script found');
  } else {
    console.log('   ❌ build script missing');
    allGood = false;
  }
  
  if (packageJson.dependencies && packageJson.dependencies.vue) {
    console.log('   ✅ Vue.js dependency found');
  } else {
    console.log('   ❌ Vue.js dependency missing');
    allGood = false;
  }
  
  if (packageJson.devDependencies && packageJson.devDependencies.vite) {
    console.log('   ✅ Vite dependency found');
  } else {
    console.log('   ❌ Vite dependency missing');
    allGood = false;
  }
} catch (error) {
  console.log('   ❌ Error reading package.json');
  allGood = false;
}

// Check vite.config.js
console.log('\n⚙️  Checking Vite configuration:');
try {
  const viteConfig = fs.readFileSync('vite.config.js', 'utf8');
  
  if (viteConfig.includes('defineConfig')) {
    console.log('   ✅ Vite config structure looks good');
  } else {
    console.log('   ❌ Vite config structure issue');
    allGood = false;
  }
  
  if (viteConfig.includes('index.html')) {
    console.log('   ✅ Entry point configured');
  } else {
    console.log('   ❌ Entry point not configured');
    allGood = false;
  }
} catch (error) {
  console.log('   ❌ Error reading vite.config.js');
  allGood = false;
}

// Check index.html
console.log('\n🌐 Checking index.html:');
try {
  const indexHtml = fs.readFileSync('index.html', 'utf8');
  
  if (indexHtml.includes('<div id="app">')) {
    console.log('   ✅ App mount point found');
  } else {
    console.log('   ❌ App mount point missing');
    allGood = false;
  }
  
  if (indexHtml.includes('src="/src/main.js"')) {
    console.log('   ✅ Main.js script reference found');
  } else {
    console.log('   ❌ Main.js script reference missing');
    allGood = false;
  }
} catch (error) {
  console.log('   ❌ Error reading index.html');
  allGood = false;
}

// Summary
console.log('\n' + '='.repeat(50));
if (allGood) {
  console.log('🎉 All checks passed! Your build setup looks good.');
  console.log('\n📋 Next steps:');
  console.log('   1. Install Node.js 18+ if not already installed');
  console.log('   2. Run: npm install');
  console.log('   3. Run: npm run build');
  console.log('   4. Check the dist/ directory for build output');
} else {
  console.log('❌ Some issues found. Please fix the missing files/dependencies.');
  console.log('\n💡 Common fixes:');
  console.log('   • Ensure index.html is in the root directory');
  console.log('   • Check that all source files exist');
  console.log('   • Verify package.json has correct dependencies');
  console.log('   • Make sure vite.config.js is properly configured');
}
console.log('='.repeat(50)); 