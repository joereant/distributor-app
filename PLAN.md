# DistributorApp — Final Plan

## 1. Ringkasan Project

**Nama:** DistributorApp
**Deskripsi:** Aplikasi manajemen distributor semen untuk user management, transaksi penjualan, dan monitoring.
**Stack:** Laravel 13 + Svelte 5 + Inertia.js + Tailwind CSS + PostgreSQL
**Pola:** Single-stack, multi-panel (ikuti pola Corsales)
**Kode:** `distributor-app/` (subfolder Laravel di root project)

---

## 2. Struktur Folder

```
DistributorApp/
├── distributor-app/          ← Laravel app (seperti corsales-api/)
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
│   └── settings.local.json
├── .gitignore
├── CLAUDE.md
├── TECH_SPECS.md
├── README.md
├── DISCUSSION_PLAN.md
├── CLIENT_PROPOSAL.md
└── DEMO_PLAN.md
```

---

## 3. Panel & Role

| Panel | Role | Akses |
|-------|------|-------|
| **Admin** | admin | Full CRUD semua modul, manajemen user, laporan lengkap |
| **Sales** | sales | Buat transaksi, lihat customer area sendiri, monitor transaksi |
| **Customer** | customer | Lihat transaksi sendiri, place order, monitor pengiriman |
| **VIP** | vip | Semua akses customer + priority support, analytics ringan |

---

## 4. Model Data Utama

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

---

## 5. Fitur MVP (Prioritas P0)

### Autentikasi
- [ ] Login (manual + Azure SSO)
- [ ] Registrasi dengan role selection
- [ ] Verifikasi admin
- [ ] Session management

### Dashboard
- [ ] Halaman utama setelah login
- [ ] Ringkasan transaksi (total hari ini, minggu ini, bulan ini)
- [ ] List transaksi terbaru
- [ ] Navigasi panel

### Manajemen Produk (Admin)
- [ ] List produk
- [ ] Tambah produk baru
- [ ] Edit & hapus produk

### Transaksi Penjualan (Sales)
- [ ] Buat transaksi baru
- [ ] Pilih customer
- [ ] Pilih produk & quantity
- [ ] Lihat ringkasan order

### Monitor Transaksi (Customer)
- [ ] Lihat transaksi sendiri
- [ ] Status transaksi

---

## 6. Fitur Tambahan (P1 — Nanti)

- Credit limit per customer
- Scheme/discount per wilayah
- Sales target tracking
- Delivery tracking real-time
- Audit trail
- Multi-gudang support
- Import data Excel
- Laporan keuangan lengkap
- Aging piutang

---

## 7. Auth Approach

- **Azure SSO** (ikuti pola Corsales)
- **Manual registration** dengan form `register_as` (ADMIN / SALES / CUSTOMER / VIP)
- **Verifikasi SUPERADMIN** wajib sebelum aktif
- **Role-based access** via middleware `CheckRole`

---

## 8. Timeline

| Fase | Durasi | Deliverable |
|------|--------|-------------|
| Demo ringan | 1-2 minggu | Auth + Dashboard + Produk + Transaksi dasar |
| MVP lengkap | 4-6 minggu | Semua fitur P0 + panel lengkap |
| Production ready | 2-4 minggu | Deploy, stabilisasi, testing |

---

## 9. Dokumen Terkait

| Dokumen | Lokasi | Isi |
|---------|--------|-----|
| Discussion Plan | `distributor-app/DISCUSSION_PLAN.md` | Topik yg harus disepakati |
| Client Proposal | `distributor-app/CLIENT_PROPOSAL.md` | Resume untuk presentasi client |
| Demo Plan | `distributor-app/DEMO_PLAN.md` | Fitur ringan untuk demo awal |
| Tech Specs | `TECH_SPECS.md` | Tech specs & arsitektur |
| Decisions | `../ObsidianDen/10_Projects/DistributorApp/DECISIONS.md` | Keputusan & guard rails |
| CLAUDE.md | `CLAUDE.md` | Aturan project |

---

## 10. Status

- [x] Folder structure created
- [x] CLAUDE.md, TECH_SPECS.md, README.md created
- [x] DECISIONS.md di ObsidianDen dibuat
- [x] SESSION_INIT.md & INDEX.md diupdate
- [x] Discussion Plan dibuat
- [x] Client Proposal dibuat
- [x] Demo Plan dibuat
- [ ] Topik diskusi fitur disepakati
- [ ] Demo dibangun
- [ ] MVP dikembangkan
- [ ] Production ready

---

*Plan ini akan diupdate seiring perkembangan diskusi dan implementasi.*