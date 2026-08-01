# DistributorApp — Demo Plan

## Tujuan Demo
Menampilkan fitur-fitur inti DistributorApp dalam bentuk ringan untuk review oleh client.

---

## Fitur Demo (MVP Ringan)

### 1. Autentikasi
- [ ] Halaman login
- [ ] Halaman registrasi (manual)
- [ ] Role selection saat registrasi (Admin / Sales / Customer)
- [ ] Session management

### 2. Dashboard
- [ ] Halaman utama setelah login
- [ ] Ringkasan transaksi (total hari ini, minggu ini, bulan ini)
- [ ] List transaksi terbaru
- [ ] Navigasi panel (Admin / Sales / Customer)

### 3. Manajemen Produk (Admin only)
- [ ] List produk
- [ ] Tambah produk baru
- [ ] Edit & hapus produk

### 4. Transaksi Penjualan (Sales only)
- [ ] Buat transaksi baru
- [ ] Pilih customer
- [ ] Pilih produk & quantity
- [ ] Lihat ringkasan order

### 5. Monitor Transaksi (Customer only)
- [ ] Lihat transaksi sendiri
- [ ] Status transaksi

---

## Panel yang Ditampilkan

| Panel | Fitur Demo |
|-------|-----------|
| **Admin** | Dashboard + Manajemen Produk |
| **Sales** | Dashboard + Buat Transaksi |
| **Customer** | Dashboard + Lihat Transaksi |

---

## Tech untuk Demo
- Laravel + Svelte + Inertia + Tailwind (same as final)
- Database: SQLite (untuk demo lokal)
- Auth: Manual (tanpa Azure SSO dulu)
- Deployment: Local / Laravel Serve

---

## Timeline Demo
- Hari 1-2: Setup project & auth
- Hari 3-4: Dashboard
- Hari 5-6: Manajemen produk & transaksi
- Hari 7: Finalisasi & presentasi

---

*Demo ini bersifat ringan dan bisa dikembangkan setelah disepakati oleh client.*