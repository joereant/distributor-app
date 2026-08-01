# DistributorApp — Technical Specs

## Overview
Aplikasi manajemen distributor semen. Digunakan oleh customer client (distributor) untuk bertransaksi dan monitoring transaksi.

## Stack
| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 (PHP) |
| Frontend | Svelte 5 + Inertia.js |
| Styling | Tailwind CSS |
| Database | PostgreSQL |
| Auth | Azure SSO + Laravel Sanctum |
| Deployment | Docker (Laravel Sail) |

## Arsitektur
Single-stack, multi-panel (pola Corsales):
- Backend API + frontend Svelte dalam 1 repo Laravel
- Inertia.js untuk server-side rendering dengan Svelte components
- Multi-panel: Admin panel, Sales panel, Customer panel, VIP panel

## Struktur Folder
```
DistributorApp/
├── distributor-app/          ← Laravel app root
│   ├── app/
│   │   ├── Console/Commands/
│   │   ├── Exports/
│   │   ├── Http/Controllers/
│   │   ├── Http/Middleware/
│   │   ├── Models/
│   │   ├── Observers/
│   │   ├── Providers/
│   │   └── Services/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── public/
│   ├── resources/
│   │   ├── css/
│   │   ├── js/
│   │   │   ├── Components/
│   │   │   ├── Layouts/
│   │   │   ├── lib/
│   │   │   └── Pages/
│   │   │       ├── Admin/
│   │   │       ├── Auth/
│   │   │       └── Sales/
│   │   └── views/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   ├── .env / .env.example
│   ├── artisan
│   ├── composer.json
│   ├── package.json
│   ├── phpunit.xml
│   └── vite.config.js
├── .claude/
├── .gitignore
├── CLAUDE.md
├── TECH_SPECS.md
└── README.md
```

## Modules (rencana)
1. **Auth & User Management** — registrasi, login, role (admin, sales, customer, vip), Azure SSO
2. **Master Data** — produk, harga, customer, area distribusi, gudang, kategori
3. **Transaksi Penjualan** — order, invoice, pembayaran, retur, delivery order
4. **Inventory** — stok real-time per gudang, alert stok rendah, mutasi antar gudang
5. **Dashboard & Monitoring** — ringkasan transaksi, penjualan per area, top produk, piutang outstanding
6. **Audit Log** — jejak perubahan data
7. **Fitur Tambahan** — credit limit, scheme/discount, sales target, delivery tracking, import Excel

## Panel & Role
| Panel | Role | Akses |
|-------|------|-------|
| Admin | admin | Full CRUD semua modul, manajemen user, laporan lengkap |
| Sales | sales | Buat transaksi, lihat customer area sendiri, monitor transaksi |
| Customer | customer | Lihat transaksi sendiri, place order, monitor pengiriman |
| VIP | vip | Semua akses customer + priority support, analytics ringan |

## Model Data Utama
| Model | Tabel | Kolom Utama |
|-------|-------|-------------|
| User | `users` | id, name, email, password, azure_id, role, status, verified_at, sales_area_id |
| Customer | `customers` | id, user_id (opsional), company_name, contact_person, phone, email, address, area_id, customer_type |
| Product | `products` | id, name, code, description, unit, price, category_id, min_stock |
| Transaction | `transactions` | id, customer_id, user_id (sales), transaction_date, total, status, payment_method, notes |
| TransactionItem | `transaction_items` | id, transaction_id, product_id, quantity, unit_price, subtotal |
| Distribution | `distributions` | id, transaction_id, product_id, quantity, destination, status, shipped_at |
| Inventory | `inventories` | id, product_id, warehouse_id, quantity, min_stock |
| Area | `areas` | id, name, code, region |
| Warehouse | `warehouses` | id, name, location, area_id |
| Category | `categories` | id, name, slug |

## Fitur MVP (P0)
- Auth: login, registrasi dengan role, verifikasi admin
- Dashboard: ringkasan transaksi, navigasi panel
- Manajemen Produk (Admin): CRUD produk
- Transaksi Penjualan (Sales): buat order, pilih customer & produk
- Monitor Transaksi (Customer): lihat transaksi sendiri

## Fitur Tambahan (P1)
- Credit limit per customer
- Scheme/discount per wilayah
- Sales target tracking
- Delivery tracking real-time
- Audit trail
- Multi-gudang support
- Import data Excel

## Auth Approach
- Azure SSO (ikuti pola Corsales)
- Manual registration dengan form `register_as` (ADMIN / SALES / CUSTOMER / VIP)
- Verifikasi SUPERADMIN wajib sebelum aktif
- Role-based access via middleware `CheckRole`

## Status
Draft — diskusi fitur belum final, menunggu konfirmasi om.