# Deployment Guide: Railway + Neon PostgreSQL

## Overview

This document covers deployment for the Warehouse Inventory application running on **Railway** with **Neon PostgreSQL** as the database.

Railway is the recommended platform for Laravel applications as it provides native PHP support and easy integration with Neon.

---

## Prerequisites

1. **Railway Account** (sign up at [railway.app](https://railway.app))
2. **Neon Account** with a PostgreSQL project created
3. **Railway CLI** installed

---

## Quick Start

### 1. Install Railway CLI

```bash
npm install -g @railway/cli
```

### 2. Login to Railway

```bash
railway login
```

### 3. Initialize Project

```bash
cd /path/to/warehouse_web
railway init
# Select "Empty Service" or "PHP"
```

### 4. Link to Existing Neon Database

```bash
# Option A: Use Railway's PostgreSQL
railway add

# Option B: Use existing Neon database
railway variables set DB_HOST=your-project.neon.tech
railway variables set DB_PORT=5432
railway variables set DB_DATABASE=warehouse_inventory
railway variables set DB_USERNAME=your-username
railway variables set DB_PASSWORD=your-password
railway variables set DB_SSLMODE=require
```

### 5. Set Environment Variables

```bash
# Application
railway variables set APP_NAME="Warehouse Inventory"
railway variables set APP_ENV=production
railway variables set APP_KEY=base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=
railway variables set APP_DEBUG=false
railway variables set APP_TIMEZONE=Asia/Jakarta
railway variables set APP_URL=https://your-app.railway.app

# Database
railway variables set DB_CONNECTION=pgsql
railway variables set DB_HOST=your-neon-host.neon.tech
railway variables set DB_PORT=5432
railway variables set DB_DATABASE=warehouse_inventory
railway variables set DB_USERNAME=your-neon-username
railway variables set DB_PASSWORD=your-neon-password
railway variables set DB_SSLMODE=require

# Session & Cache
railway variables set SESSION_DRIVER=database
railway variables set SESSION_LIFETIME=120
railway variables set CACHE_STORE=database

# Queue
railway variables set QUEUE_CONNECTION=sync
```

### 6. Deploy

```bash
# Deploy to Railway
railway up

# Check deployment status
railway status
```

### 7. Run Migrations

```bash
railway run php artisan migrate --force
```

### 8. Open in Browser

```bash
railway open
```

---

## Railway Configuration

### healthcheck.json (optional)

Create `healthcheck.json` in project root:

```json
{
  "path": "/up",
  "port": 8000,
  "intervalSeconds": 30,
  "timeoutSeconds": 10
}
```

### Start Command

Railway will auto-detect PHP and run `composer install`. If needed, set:

**Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`

Set this in Railway Dashboard → Service → Settings → Start Command

---

## Environment Variables

### Required Variables (Production)

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_NAME` | Application name | `Warehouse Inventory` |
| `APP_ENV` | Environment | `production` |
| `APP_KEY` | Laravel application key | `base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_TIMEZONE` | Server timezone | `Asia/Jakarta` |
| `APP_URL` | Application URL | `https://your-app.railway.app` |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Neon host | `your-project.neon.tech` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `warehouse_inventory` |
| `DB_USERNAME` | Database user | `your-username` |
| `DB_PASSWORD` | Database password | `(your-neon-password)` |
| `DB_SSLMODE` | SSL mode | `require` |

### Optional Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `SESSION_DRIVER` | Session storage | `database` |
| `SESSION_LIFETIME` | Session lifetime (minutes) | `120` |
| `QUEUE_CONNECTION` | Queue driver | `sync` |
| `CACHE_STORE` | Cache driver | `database` |
| `CACHE_PREFIX` | Cache prefix | `warehouse_` |
| `LOG_CHANNEL` | Log channel | `stack` |
| `LOG_LEVEL` | Log verbosity | `info` |
| `BCRYPT_ROUNDS` | Password hashing rounds | `12` |
| `FILESYSTEM_DISK` | File storage | `public` |

---

## Database Setup (Neon)

### 1. Create Neon Project

1. Go to [Neon Console](https://console.neon.tech)
2. Create a new project
3. Copy the connection details

### 2. Connection String Format

```
postgresql://username:password@host:5432/database?sslmode=require
```

### 3. Allow Railway IP

In Neon dashboard, go to Connection Details → Allow IP and add Railway's IP ranges or enable "Allow all IPv4 connections" for development.

---

## Deployment via GitHub (Recommended)

### 1. Connect GitHub Repository

1. Go to [Railway Dashboard](https://railway.app)
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your repository

### 2. Configure Deployment

Railway will auto-detect Laravel and run:
- `composer install`
- Build assets (if using Vite)

### 3. Add Variables

In Railway Dashboard → Service → Variables, add all required environment variables.

### 4. Add PostgreSQL

1. Click "Add a Database" → "PostgreSQL"
2. Or connect to existing Neon: Add DB_HOST, DB_USERNAME, etc.

### 5. Deploy

Railway will automatically deploy on every push to `main`.

---

## Migration Files

### Core Migrations (database/migrations/)

| File | Description |
|------|-------------|
| `0001_01_01_000000_create_users_table.php` | Users, sessions, password reset |
| `0001_01_01_000001_create_cache_table.php` | Cache table |
| `0001_01_01_000002_create_jobs_table.php` | Queue jobs |
| `2024_01_01_000001_create_spatie_permission_tables.php` | Spatie roles/permissions |

### Module Migrations

| Module | Tables |
|--------|--------|
| Product | `products` |
| Inventory | `inventory_transactions`, `stock_ledger` |
| TaskCenter | `tasks`, `task_activity_logs` |
| StockOpname | `stock_opname_sessions`, `stock_opname_items`, `stock_opname_assignments`, `stock_opname_activity_logs` |
| Approval | `approval_requests`, `approval_decisions`, `approval_activity_logs` |

---

## Routes Summary

### Module Routes

| Module | Prefix | Middleware | Description |
|--------|--------|------------|-------------|
| Users | `/users` | auth, verified | User management |
| Product | `/products` | auth, verified | Product catalog |
| Inventory | `/inventory` | auth, verified | Stock operations |
| TaskCenter | `/tasks` | auth, verified | Task management |
| StockOpname | `/stock-opnames` | auth, verified | Stock counting |
| Approval | `/approvals` | auth, verified | Approval workflow |
| Operator Dashboard | `/dashboard` | auth, verified | Operator view |
| Admin Dashboard | `/dashboard/admin` | auth, verified, role:SuperAdmin\|WarehouseAdmin | Admin view |

---

## Troubleshooting

### 500 Internal Server Error

1. Check `APP_KEY` is set correctly
2. Verify database connection
3. Check logs: `railway logs`

### Database Connection Failed

1. Verify `DB_HOST`, `DB_PORT`, `DB_DATABASE`
2. Ensure Neon allowlist includes Railway IPs
3. Check `DB_SSLMODE=require`

### Migrations Failed

```bash
# Run migrations manually
railway run php artisan migrate --force
```

---

## Useful Commands

```bash
# View logs
railway logs

# Open shell
railway shell

# Run artisan commands
railway run php artisan <command>

# Check environment variables
railway variables

# Redeploy
railway redeploy
```

---

## Support

- **Railway Docs**: [docs.railway.app](https://docs.railway.app)
- **Neon Docs**: [neon.tech/docs](https://neon.tech/docs)
- **Laravel Docs**: [laravel.com/docs](https://laravel.com/docs)