#!/bin/bash

# Billing Pages - Build Script
# This script builds the application for production

set -e

echo "🔨 Building Billing Pages application..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${YELLOW}[BUILD]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    print_error "Node.js is not installed. Please install Node.js 18+ first."
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node --version | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    print_error "Node.js version 18+ is required. Current version: $(node --version)"
    exit 1
fi

print_status "Node.js version: $(node --version)"

# Check if package.json exists
if [ ! -f "package.json" ]; then
    print_error "package.json not found. Are you in the correct directory?"
    exit 1
fi

# Clean previous build
print_status "Cleaning previous build..."
rm -rf dist
rm -rf node_modules/.vite

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    print_status "Installing dependencies..."
    npm install
fi

# Check if index.html exists in root
if [ ! -f "index.html" ]; then
    print_error "index.html not found in root directory"
    exit 1
fi

# Build the application
print_status "Building application..."
npm run build

# Check if build was successful
if [ ! -d "dist" ]; then
    print_error "Build failed - dist directory not created"
    exit 1
fi

# Check if main files exist
if [ ! -f "dist/index.html" ]; then
    print_error "Build failed - index.html not found in dist"
    exit 1
fi

if [ ! -d "dist/assets" ]; then
    print_error "Build failed - assets directory not found in dist"
    exit 1
fi

# Copy assets from public to dist if they exist
if [ -d "public" ]; then
    print_status "Copying public assets..."
    cp -r public/* dist/ 2>/dev/null || true
fi

# Set proper permissions
print_status "Setting permissions..."
chmod -R 755 dist

# Display build summary
print_success "Build completed successfully!"
echo ""
echo "📁 Build Output:"
echo "   • Location: $(pwd)/dist"
echo "   • Size: $(du -sh dist | cut -f1)"
echo "   • Files: $(find dist -type f | wc -l)"
echo ""
echo "🚀 To serve the application:"
echo "   • Development: npm run dev"
echo "   • Production: npx serve dist"
echo "   • Or copy dist/ to your web server"
echo ""

# Optional: Create a simple server for testing
if command -v npx &> /dev/null; then
    echo "💡 To test the build locally:"
    echo "   npx serve dist"
fi 