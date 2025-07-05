@echo off
REM Billing Pages - Windows Build Script
REM This script builds the application for production on Windows

echo 🔨 Building Billing Pages application...

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js is not installed. Please install Node.js 18+ first.
    pause
    exit /b 1
)

REM Check Node.js version
for /f "tokens=1,2 delims=." %%a in ('node --version') do set NODE_VERSION=%%a
set NODE_VERSION=%NODE_VERSION:~1%
if %NODE_VERSION% LSS 18 (
    echo ❌ Node.js version 18+ is required. Current version: 
    node --version
    pause
    exit /b 1
)

echo ✅ Node.js version: 
node --version

REM Check if package.json exists
if not exist "package.json" (
    echo ❌ package.json not found. Are you in the correct directory?
    pause
    exit /b 1
)

REM Check if index.html exists in root
if not exist "index.html" (
    echo ❌ index.html not found in root directory
    echo 💡 Copying from public directory...
    copy "public\index.html" "index.html" >nul 2>&1
    if not exist "index.html" (
        echo ❌ Failed to copy index.html
        pause
        exit /b 1
    )
    echo ✅ index.html copied to root directory
)

REM Clean previous build
echo 📁 Cleaning previous build...
if exist "dist" rmdir /s /q "dist"
if exist "node_modules\.vite" rmdir /s /q "node_modules\.vite"

REM Install dependencies if node_modules doesn't exist
if not exist "node_modules" (
    echo 📦 Installing dependencies...
    npm install
    if %errorlevel% neq 0 (
        echo ❌ Failed to install dependencies
        pause
        exit /b 1
    )
)

REM Build the application
echo 🔨 Building application...
npm run build
if %errorlevel% neq 0 (
    echo ❌ Build failed
    pause
    exit /b 1
)

REM Check if build was successful
if not exist "dist" (
    echo ❌ Build failed - dist directory not created
    pause
    exit /b 1
)

if not exist "dist\index.html" (
    echo ❌ Build failed - index.html not found in dist
    pause
    exit /b 1
)

if not exist "dist\assets" (
    echo ❌ Build failed - assets directory not found in dist
    pause
    exit /b 1
)

REM Copy assets from public to dist if they exist
if exist "public" (
    echo 📁 Copying public assets...
    xcopy "public\*" "dist\" /E /I /Y >nul 2>&1
)

echo.
echo ✅ Build completed successfully!
echo.
echo 📁 Build Output:
echo    • Location: %CD%\dist
echo    • Files: 
dir /b dist | find /c /v "" >nul 2>&1
echo.
echo 🚀 To serve the application:
echo    • Development: npm run dev
echo    • Production: npx serve dist
echo    • Or copy dist\ to your web server
echo.

REM Optional: Create a simple server for testing
where npx >nul 2>&1
if %errorlevel% equ 0 (
    echo 💡 To test the build locally:
    echo    npx serve dist
)

pause 