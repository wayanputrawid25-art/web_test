#!/bin/bash
# Vercel Build Script for Laravel

echo "Starting build process..."

# Navigate to application directory
cd /workspace/project/warehouse_web

# Install dependencies
echo "Installing dependencies..."
composer install --optimize-autoloader --no-dev

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY not set. Please set it in environment variables."
fi

# Clear and rebuild cache
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear

# Build assets (if using Vite)
if [ -f "vite.config.js" ]; then
    echo "Building assets..."
    npm run build
fi

# Run database migrations (only in production)
if [ "$APP_ENV" = "production" ]; then
    echo "Running migrations..."
    php artisan migrate --force --no-interaction
fi

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build complete!"