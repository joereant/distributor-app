# DistributorApp

Aplikasi manajemen distributor semen — user management, transaksi penjualan, dan monitoring.

## Stack
- Backend: Laravel 13 (PHP)
- Frontend: Svelte 5 + Inertia.js
- Styling: Tailwind CSS
- Database: PostgreSQL
- Auth: Azure SSO + Laravel Sanctum

## Setup

```bash
cd distributor-app

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start dev server
php artisan serve

# Start Vite dev server
npm run dev
```

## Project Structure

```
DistributorApp/
├── distributor-app/    ← Laravel app
├── .claude/
├── .gitignore
├── CLAUDE.md
├── TECH_SPECS.md
└── README.md
```