# ===========================================
# ENVIRONMENT VARIABLES CHECKLIST
# ===========================================
# Copy this section to Vercel Dashboard → Settings → Environment Variables
# ===========================================

# ===========================================
# REQUIRED VARIABLES (WAJIB DIISI)
# ===========================================

# APPLICATION
APP_NAME="Warehouse Inventory"
APP_ENV=production
APP_KEY=base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-app.vercel.app  # Replace with your actual Vercel URL

# DATABASE - PostgreSQL (Neon)
DB_CONNECTION=pgsql
DB_HOST=                        # REQUIRED: your-project.neon.tech
DB_PORT=5432
DB_DATABASE=warehouse_inventory
DB_USERNAME=                    # REQUIRED: Your Neon username
DB_PASSWORD=                    # REQUIRED: Your Neon password
DB_SSLMODE=require

# ===========================================
# OPTIONAL VARIABLES
# ===========================================

# SESSION & CACHE
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
CACHE_STORE=database
CACHE_PREFIX=warehouse_

# QUEUE
QUEUE_CONNECTION=sync

# LOGGING
LOG_CHANNEL=stack
LOG_LEVEL=info

# SECURITY
BCRYPT_ROUNDS=12

# FILE STORAGE
FILESYSTEM_DISK=public

# LARAVEL SETTINGS
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=file

# ===========================================
# HOW TO GENERATE APP_KEY
# ===========================================
# 1. Run locally: php artisan key:generate
# 2. Copy the generated key (starts with base64:)
# 3. Paste as APP_KEY value
#
# Or use: base64:$(openssl rand -base64 32)
# ===========================================

# ===========================================
# NEON CONNECTION STRING FORMAT
# ===========================================
# Format: postgresql://username:password@host:5432/database?sslmode=require
#
# Example:
# postgresql://myuser:mypass@myproject.neon.tech:5432/warehouse_inventory?sslmode=require
# ===========================================

# ===========================================
# QUICK SETUP COMMAND
# ===========================================
# Run this locally to generate all required variables:
#
# vercel env add APP_NAME
# vercel env add APP_ENV
# vercel env add APP_KEY
# vercel env add APP_DEBUG
# vercel env add APP_TIMEZONE
# vercel env add APP_URL
# vercel env add DB_CONNECTION
# vercel env add DB_HOST
# vercel env add DB_PORT
# vercel env add DB_DATABASE
# vercel env add DB_USERNAME
# vercel env add DB_PASSWORD
# vercel env add DB_SSLMODE
# ===========================================