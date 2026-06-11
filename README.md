# Warehouse Inventory System

Inventory Management System dengan arsitektur **Modular Monolith** menggunakan Laravel 12, Livewire 3, PostgreSQL, dan Pest.

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire 3 + TailwindCSS 4
- **Database**: PostgreSQL
- **Authorization**: Spatie Permission
- **Testing**: Pest
- **Build Tool**: Vite

## Arsitektur Modular Monolith

### Konsep

Modular Monolith adalah arsitektur di mana codebase dibagi menjadi modul-modul yang independen, namun tetap berjalan dalam satu aplikasi monolith. Setiap modul memiliki struktur internal yang lengkap seperti aplikasi kecil.

### Struktur Direktori

```
app/
├── Modules/
│   ├── Product/           # Manajemen Produk
│   ├── Inventory/         # Manajemen Inventory
│   ├── Purchase/          # Pembelian / Purchase Order
│   ├── Sales/             # Penjualan / Sales Order
│   ├── StockOpname/       # Pencatatan Stock Opname
│   ├── TaskCenter/        # Pusat Tugas / Task Management
│   ├── Approval/          # Sistem Approval / Workflow
│   ├── Reports/           # Laporan & Analitik
│   └── Users/             # Manajemen User & Role
├── Models/                 # Shared Models
├── Providers/             # Service Providers
└── Services/              # Shared Services
```

### Struktur Modul

Setiap modul memiliki struktur internal berikut:

```
ModuleName/
├── Actions/               # Action Classes (Business Logic)
├── Channels/              # Notification Channels
├── Commands/              # Artisan Commands
├── Controllers/           # HTTP Controllers
├── Data/                  # Data Transfer Objects
├── DTOs/                  # Data Transfer Objects
├── Events/                # Domain Events
├── Exceptions/            # Custom Exceptions
├── Factories/             # Model Factories
├── Http/
│   ├── Controllers/       # API/Web Controllers
│   ├── Middleware/        # Module-specific Middleware
│   ├── Requests/          # Form Requests
│   └── Resources/         # API Resources
├── Jobs/                  # Queue Jobs
├── Listeners/             # Event Listeners
├── Mail/                  # Mail Templates
├── Models/                # Eloquent Models
├── Notifications/         # Notification Classes
├── Observers/             # Model Observers
├── Providers/             # Module Service Provider
├── Queries/               # Query Builder Classes
├── Resources/             # View Resources
├── Rules/                 # Validation Rules
├── Services/              # Business Services
└── States/                # State Machines
```

### Prinsip Modular

1. **Independensi**: Setiap modul dapat berkembang secara independen
2. **Kohesi Tinggi**: Kode yang berkaitan berada dalam satu modul
3. **Coupling Rendah**: Modul berkomunikasi melalui interface yang jelas
4. **Bounded Context**: Setiap modul memiliki context sendiri

## Roles

| Role | Deskripsi |
|------|----------|
| **SuperAdmin** | Akses penuh ke semua fitur sistem |
| **WarehouseAdmin** | Mengelola inventory, purchase, dan stock opname |
| **Operator** | Mengoperasikan sistem sesuai tugas yang diberikan |

## Installation

### Prerequisites

- PHP 8.2+
- PostgreSQL 14+
- Composer 2+
- Node.js 20+
- npm / pnpm

### Setup

1. Clone repository:
```bash
git clone <repository-url>
cd warehouse_web
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Setup environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure PostgreSQL in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=warehouse_inventory
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
php artisan db:seed
```

6. Build assets:
```bash
npm run build
```

7. Start development server:
```bash
php artisan serve
```

## Development

### Module Registration

Untuk menambahkan modul baru, buat service provider di `app/Modules/{ModuleName}/Providers/`:

```php
<?php

namespace App\Modules\ModuleName\Providers;

use App\Modules\ModuleServiceProvider;

class ModuleNameServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ModuleName';
}
```

Lalu daftarkan di `app/Providers/ModuleServiceProvider.php`.

### Creating Models

Models untuk modul diletakkan di `app/Modules/{ModuleName}/Models/`.

### Creating Controllers

Controllers diletakkan di `app/Modules/{ModuleName}/Http/Controllers/`.

### Database Migrations

Migrations untuk modul diletakkan di `app/Modules/{ModuleName}/Database/Migrations/`.

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Unit
```

## License

MIT License
