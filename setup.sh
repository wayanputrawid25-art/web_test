#!/bin/bash

# Warehouse Inventory - Setup Script
set -e

echo "🚀 Setting up Warehouse Inventory System..."

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm install

# Create environment file
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed

# Build assets
echo "🎨 Building assets..."
npm run build

echo "✅ Setup complete!"