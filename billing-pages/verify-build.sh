#!/bin/bash

# Build verification script for billing-pages.com
# This script tests the build process on Ubuntu 24.04

set -e

echo "🔧 Starting build verification for billing-pages.com..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Installing Node.js 18.x..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

# Check if npm is available
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not available. Please install Node.js properly."
    exit 1
fi

echo "✅ Node.js version: $(node --version)"
echo "✅ npm version: $(npm --version)"

# Install dependencies
echo "📦 Installing dependencies..."
npm install

# Test the build
echo "🔨 Testing build process..."
npm run build

# Check if build was successful
if [ -d "dist" ]; then
    echo "✅ Build successful! Dist folder created."
    echo "📁 Build contents:"
    ls -la dist/
    
    # Check for main CSS file
    if [ -f "dist/assets/index-*.css" ]; then
        echo "✅ CSS file generated successfully"
    else
        echo "⚠️  CSS file not found in expected location"
        find dist/ -name "*.css" -type f
    fi
    
    # Check for main JS file
    if [ -f "dist/assets/index-*.js" ]; then
        echo "✅ JavaScript file generated successfully"
    else
        echo "⚠️  JavaScript file not found in expected location"
        find dist/ -name "*.js" -type f
    fi
    
    echo "🎉 Build verification completed successfully!"
else
    echo "❌ Build failed! Dist folder not created."
    exit 1
fi 