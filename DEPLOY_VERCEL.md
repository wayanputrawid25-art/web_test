# Deployment Guide: Vercel + Neon PostgreSQL

## ⚠️ IMPORTANT: Vercel PHP Support Status

**Vercel NO LONGER supports PHP.** The `@vercel/php` package has been removed from npm and is no longer available.

### Current Status (June 2025)
- ❌ `@vercel/php` is **NOT published** on npm registry
- ❌ No community PHP builders available
- ❌ Vercel officially supports: Node.js, Python, Ruby, Go, Rust, Deno

---

## Alternative Deployment Platforms for Laravel

### Recommended Alternatives

| Platform | Free Tier | Notes |
|----------|-----------|-------|
| **Railway** | 500 hours/month | Best for Laravel, easy Neon integration |
| **Render** | 750 hours/month | Good PHP support, managed PostgreSQL |
| **Laravel Cloud** | - | Official Laravel hosting by Taylor Otwell |
| **Fly.io** | 3 shared VMs | Good PHP support, global edge |

### Railway (Recommended for this project)

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Initialize project
railway init

# Add PostgreSQL
railway add

# Deploy
railway up

# Set environment variables
railway variables set APP_KEY=base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=
railway variables set DB_HOST=your-neon-host.neon.tech
# ... other variables
```

### Render (Alternative)

1. Create Web Service on [render.com](https://render.com)
2. Connect GitHub repository
3. Set build command: `composer install`
4. Set start command: `php artisan serve --port=$PORT`
5. Add PostgreSQL database from dashboard

---

## If You Still Want Vercel

You can try using **Laravel Octane** with **Swoole** or **RoadRunner** converted to PHP binaries, but this is complex and not recommended for production.

**vercel.json configuration (deprecated, likely won't work):**

```json
{
  "framework": null,
  "version": 2,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/public/index.php"
    }
  ]
}
```

> ⚠️ **This will fail** because `@vercel/php` package does not exist on npm.

---

## For Current Vercel Deployment

If you must use Vercel, consider **rewriting the backend in Node.js** using:
- Express.js or Fastify for API
- Prisma or Drizzle for database ORM
- Keep the Livewire components as standalone Vue/React components

This is a significant rewrite and not recommended.

---

## Environment Variables (Wajib Diisi)

Set these in Vercel Dashboard → Settings → Environment Variables:

### Required Variables (Production)

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_NAME` | Application name | `Warehouse Inventory` |
| `APP_ENV` | Environment | `production` |
| `APP_KEY` | Laravel application key | `base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_TIMEZONE` | Server timezone | `Asia/Jakarta` |
| `APP_URL` | Application URL | `https://your-app.vercel.app` |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Neon host | `your-project.neon.tech` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `warehouse_inventory` |
| `DB_USERNAME` | Database user | `your-username` |
| `DB_PASSWORD` | Database password | `(your-neon-password)` |
| `DB_SSLMODE` | SSL mode | `require` |

### Generate APP_KEY

> **⚠️ Production APP_KEY sudah di-generate:**
> ```
> base64:dSMZ6cWwzUSvQq78dOZVfBwMc338doBYjEnt9I/Ik4A=
> ```

Untuk generate baru:
```bash
php artisan key:generate --show
```

Atau:
```bash
base64:$(openssl rand -base64 32)
```

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

### 3. Run Migrations

After deployment, run migrations via Vercel CLI:

```bash
# Pull environment variables
vercel env pull

# Run migrations
php artisan migrate --force
```

---

## Deployment Steps

### 1. Install Vercel CLI

```bash
npm install -g vercel
```

### 2. Login to Vercel

```bash
vercel login
```

### 3. Deploy

```bash
# Navigate to project
cd /path/to/warehouse_web

# Deploy to preview
vercel

# Deploy to production
vercel --prod
```

### 4. Set Environment Variables

```bash
# Add each variable
vercel env add APP_KEY
vercel env add DB_HOST
# ... etc
```

Or set via Vercel Dashboard:

1. Go to Project Settings
2. Navigate to Environment Variables
3. Add each variable from the table above

### 5. Run Initial Setup

```bash
# SSH into the deployment (if needed)
vercel bash

# Run migrations
php artisan migrate --force

# Clear and optimize caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Vercel Configuration

The `vercel.json` file is configured with:

- **PHP 8.2 runtime** support
- **Singapore region** for optimal Asia-Pacific performance
- **Security headers** (X-Content-Type-Options, X-Frame-Options, etc.)
- **Static file caching** for CSS/JS/Images

### Build Output

```
public/
├── index.php          # Laravel entry point
├── css/               # Compiled styles
├── js/                # Compiled scripts
└── images/            # Static images
```

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

## Security Checklist

- [ ] `APP_KEY` is set (32-byte base64 key)
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Database password is strong
- [ ] SSL is enabled for database (`DB_SSLMODE=require`)
- [ ] CSRF protection is enabled (Laravel default)
- [ ] Rate limiting is configured

---

## Troubleshooting

### 500 Internal Server Error

1. Check `APP_KEY` is set correctly
2. Verify database connection
3. Check logs: `vercel logs <deployment-url>`

### Database Connection Failed

1. Verify `DB_HOST`, `DB_PORT`, `DB_DATABASE`
2. Ensure Neon allowlist includes your IP (or use SSL)
3. Check `DB_SSLMODE=require`

### Migrations Failed

```bash
# Check migration status
php artisan migrate:status

# Force migrations (use with caution)
php artisan migrate --force
```

---

## Useful Commands

```bash
# View routes
php artisan route:list

# Clear cache
php artisan cache:clear

# Clear config
php artisan config:clear

# Rebuild cached configs
php artisan config:cache

# View registered modules
php artisan module:list
```

---

## Support

For issues related to:
- **Vercel**: [Vercel Docs](https://vercel.com/docs)
- **Neon**: [Neon Docs](https://neon.tech/docs)
- **Laravel**: [Laravel Docs](https://laravel.com/docs)

---

## Notes

- **Queue**: Set to `sync` for simplicity. For production, consider Vercel's background functions or an external queue like Redis.
- **Sessions**: Use `database` driver with Neon for stateless deployments.
- **File Storage**: For production uploads, integrate with S3 or similar.
- **Cache**: Database driver is used by default. For better performance, consider Redis.