# DistributorApp — Client Proposal

## Pengantar

**DistributorApp** adalah aplikasi manajemen distributor semen yang dirancang untuk membantu distributor dalam mengelola transaksi penjualan, memantau stok, dan mengakses laporan secara real-time.

---

## Masalah

Distributor semen saat ini masih mengandalkan proses manual untuk:
- Mencatat transaksi penjualan
- Memantau stok inventory
- Mengelola data customer
- Membuat laporan penjualan

Proses manual ini rentan terhadap kesalahan, lambat, dan tidak memiliki visibility real-time.

---

## Solusi

DistributorApp menyediakan platform digital terpusat yang memungkinkan:

### 1. Manajemen User & Role
- **Admin**: Mengelola seluruh aplikasi, user, dan pengaturan
- **Sales**: Membuat dan memantau transaksi penjualan
- **Customer**: Melihat transaksi dan memesan produk
- **VIP**: Akses customer dengan fitur prioritas

### 2. Manajemen Produk & Inventory
- Katalog produk semen dengan harga terkini
- Monitoring stok real-time
- Peringatan stok rendah

### 3. Transaksi Penjualan
- Buat order dan invoice
- Lacak status pembayaran
- Riwayat transaksi lengkap

### 4. Dashboard & Laporan
- Ringkasan penjualan harian/mingguan/bulanan
- Laporan berdasarkan area distribusi
- Visualisasi data yang jelas

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 13 (PHP) |
| Frontend | Svelte 5 + Inertia.js |
| Styling | Tailwind CSS |
| Database | PostgreSQL |
| Auth | Azure SSO + Laravel Sanctum |
| Deployment | Docker / Laravel Sail |

---

## Demo

Kami akan membuat **demo ringan** terlebih dahulu untuk menampilkan fitur-fitur inti:
- Login & registrasi user
- Dashboard dengan ringkasan transaksi
- Manajemen produk sederhana
- Form transaksi penjualan

---

## Timeline

| Fase | Durasi | Deliverable |
|------|--------|-------------|
| Demo | 1-2 minggu | Demo ringan fitur inti |
| MVP | 4-6 minggu | Aplikasi functional |
| Production | 2-4 minggu | Deploy & stabilisasi |

---

## Kontak

Untuk diskusi lebih lanjut, hubungi tim development.

---

*Dokumen ini adalah proposal awal dan dapat disesuaikan berdasarkan diskusi lebih lanjut.*