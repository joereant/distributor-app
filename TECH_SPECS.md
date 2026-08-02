# DistributorApp — Technical Specs

## Overview
Aplikasi manajemen distributor semen. Digunakan oleh distributor (client) untuk menerima order dari customer, menghitung estimasi ongkir, memproses transaksi, dan monitoring penjualan.

## Stack
| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 (PHP) |
| Frontend | Svelte 5 + Inertia.js |
| Styling | Tailwind CSS |
| Database | PostgreSQL |
| Auth | Google (Gmail) OAuth + manual login |
| Deployment | Belum diputuskan (Docker tidak terpasang di PC dev) |

## Arsitektur
Single-stack, multi-panel (pola Corsales):
- Backend API + frontend Svelte dalam 1 repo Laravel
- Inertia.js untuk server-side rendering dengan Svelte components
- Multi-panel: Owner panel, Admin panel, Sales panel, Customer panel

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
│   │   │       ├── Customer/
│   │   │       ├── Owner/
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
├── PLAN.md
├── README.md
├── PRESENTATION_KERANGKA.md
└── Proposal_DistributorApp.pptx
```

## Alur Bisnis
```
Customer order (pilih produk + input tujuan kirim)
  → sistem hitung estimasi ongkir (produk + ongkir = total)
  → submit → status PENDING
  → Admin approve + lihat proyeksi margin
  → APPROVED → diproses & dikirim → status DONE
  → semua panel memantau status real-time
```

## Modules (rencana)
1. **Auth & User Management** — login Google (Gmail) + manual, registrasi, verifikasi admin, role (owner, admin, sales, customer)
2. **Master Data** — produk, harga per area, harga beli, customer, area, ongkir, kategori
3. **Transaksi Penjualan** — order online customer, approve admin, hitung total (produk + ongkir), proyeksi margin
4. **Sistem Referal** — lacak customer yang beli via non-sales
5. **Dashboard & Monitoring** — ringkasan penjualan, transaksi terbaru, status per panel
6. **Laporan** — rekap penjualan dasar (export)
7. **Fitur Upgrade (L1/L2)** — analisa transaksi, piutang & kredit, target komisi, integrasi jasa pengiriman, notifikasi otomatis, multi-gudang

## Panel & Role
| Panel | Role | Akses |
|-------|------|-------|
| Owner | owner | Akses penuh Admin + Sales, monitoring seluruh penjualan distributor |
| Admin | admin | Kelola data, user, harga, ongkir, approve order, laporan |
| Sales | sales | Monitoring area sendiri + customer referal (read-only, tanpa buat order) |
| Customer | customer | Order online, input tujuan kirim, pantau status & pengiriman |

## Model Data Utama
| Model | Tabel | Kolom Utama |
|-------|-------|-------------|
| User | `users` | id, name, email, password, google_id, role, status, verified_at, sales_area_id |
| Customer | `customers` | id, user_id (opsional), company_name, contact_person, phone, email, address, area_id, customer_type |
| Product | `products` | id, name, code, description, unit, price, harga_beli, category_id, min_stock |
| ProductPrice | `product_prices` | id, product_id, area_id, price (harga per area dari produsen) |
| Plant | `plants` | id, name, location, area_id |
| ShippingRate | `shipping_rates` | id, plant_id, area_id, rate (tarif ongkir Franco) |
| Transaction | `transactions` | id, customer_id, sales_id, plant_id, transaction_date, subtotal, ongkir, harga_beli, total, margin_estimate, margin_status, status, payment_method, notes |
| TransactionItem | `transaction_items` | id, transaction_id, product_id, quantity, unit_price, subtotal |
| Distribution | `distributions` | id, transaction_id, product_id, quantity, destination, status, shipped_at |
| Referal | `referals` | id, customer_id, transaction_id, referral_code |
| Area | `areas` | id, name, code, region |
| Warehouse | `warehouses` | id, name, location, area_id |
| Category | `categories` | id, name, slug |

## Fitur Awal (Fase 1) — P0
- Auth: login Google (Gmail) + manual, kedua cara bisa buat user, verifikasi admin
- 4 panel & role dengan dashboard masing-masing
- Dashboard: ringkasan penjualan, transaksi terbaru, navigasi panel
- Katalog produk + harga per area
- Manajemen Produk & Harga (Admin): CRUD produk, set harga per area, harga beli
- Area & Ongkir (Admin): CRUD area + tarif ongkir Franco
- Order Online (Customer): pilih produk, input tujuan, hitung ongkir otomatis, total = produk + ongkir
- Approve Order (Admin): review pending order + proyeksi margin (fitur standar)
- Sistem Referal: lacak customer beli via non-sales
- Monitoring Status: transaksi terlihat di semua panel
- Laporan penjualan dasar

## Fitur Upgrade
### Level 1 — Business
- Analisa transaksi (tren penjualan, produk terlaris, pola musiman)
- Piutang & kredit (limit kredit, aging, status pembayaran)
- Target & komisi sales
- Laporan lengkap (export Excel/PDF) + audit trail

### Level 2 — Enterprise
- Integrasi jasa pengiriman (API ekspedisi: ongkir akurat & tracking resi real-time)
- Notifikasi otomatis (WhatsApp/Email): status order & piutang
- Multi-gudang & manajemen stok

## Auth Approach
- Login Google (Gmail) OAuth + manual login/registration
- Kedua cara bisa membuat user (tidak semua customer punya Gmail)
- Verifikasi admin wajib sebelum akun aktif
- Role-based access via middleware `CheckRole`

## Breakpoint & Responsive (referensi)
Basis breakpoint Tailwind default. **Satu titik transisi `lg` (1024px)** — konsisten di semua halaman (panel & auth).

### Panel (sidebar / bottom-nav)
| Breakpoint | Target layar | Sidebar | Top bar | Bottom nav | Main content |
|---|---|---|---|---|---|
| < 640px | HP kecil | — | sticky | max 5 item | px-4 py-5 pb-24 |
| sm (640–767) | HP besar | — | sticky | max 5 item | px-5 pb-24 |
| md (768–1023) | Tablet portrait | — | sticky | max 5 item | px-6 pb-24 |
| lg (1024–1279) | Tablet landscape / laptop kecil | collapsed default `w-20` | — | — | lg:pl-20 + px-8 |
| xl (1280–1535) | Laptop | bebas expand `w-64` | — | — | lg:pl-64 + px-8 |
| 2xl (1536+) | Desktop besar / 4K | bebas | — | — | px-8 + max-w-7xl mx-auto |

### Aturan menu (simple & premium)
- Maksimal **5 menu per role**; **Dashboard wajib**.
- Sidebar collapsed → ikon besar center; bottom-nav → 5 slot merata.
- Tidak ada submenu berlapis.

### Auth (login/register)
| Breakpoint | Layout |
|---|---|
| < lg | Stack vertikal: brand panel atas (min-h-260px) + form bawah |
| ≥ lg | Split 2 kolom: brand panel kiri (min-h-screen) + form kanan center |

## Status
Final — desain fitur & panel sudah disepakati. Scaffold Laravel sudah jalan (Sesi 22): auth lengkap (login manual + Google + register), layout per role sidebar/bottom-nav (Sesi 23).
