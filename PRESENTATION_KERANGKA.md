# DistributorApp — Kerangka PPT Proposal & Prompt NotebookLM

> Dipakai buat presentasi penawaran ke calon client (distributor semen).
> Copy kerangka ke NotebookLM Google, lalu minta generate PPT.
> Branding: **JR Logix**. Jangan menyebut produsen/merk semen tertentu (pakai kata "produsen" saja).

---

## A. Kerangka PPT (17 slide)

### 1. Cover
- Judul besar: **DistributorApp**
- Subjudul: Aplikasi Manajemen Distributor Semen — Order Online, Estimasi Ongkir, & Monitoring Penjualan
- Footer: Proposal Penawaran — JR Logix
- Gaya: clean, profesional, latar terang, aksen merah + kuning

### 2. Agenda
- Masalah & Peluang
- Solusi: DistributorApp
- Fitur Awal (Fase 1)
- Fitur Upgrade (2 level)
- Implementasi & Biaya
- Penutup

### 3. Tentang JR Logix
- Pengembang aplikasi custom untuk bisnis UMKM & distribusi
- Fokus: aplikasi yang mudah dipakai, berorientasi hasil

### 4. Masalah di Bisnis Distributor
- Order masih manual (telpon/WA/catatan), sering salah & hilang
- Ongkir dihitung asal, margin tidak terlihat
- Owner tidak bisa pantau penjualan secara real-time
- Data customer & harga tidak terpusat
- TIDAK menyebut nama produsen — cukup "produsen"

### 5. Solusi: DistributorApp
- Satu aplikasi: customer order sendiri, sales fokus area, admin & owner kontrol penuh
- Hitungan transaksi transparan: produk + ongkir
- Nilai plus: **estimasi cuan/rugi per order**

### 6. Alur Bisnis
- Customer order (pilih produk + input tujuan)
- Sistem hitung estimasi ongkir
- Total transaksi (produk + ongkir)
- Admin approve + lihat estimasi cuan/rugi
- Order diproses & dikirim
- Semua panel memantau status real-time
- Bisa digambar sebagai diagram 6 langkah panah

### 7. Panel & Role
- **Owner** — akses penuh Admin + Sales, monitoring semua
- **Admin** — kelola data, approve order, laporan
- **Sales** — monitoring area sendiri (tanpa buat order)
- **Customer** — order online + pantau status

### 8. Panel Admin
- Dashboard & grafik penjualan
- User management
- Produk, harga jual, harga beli, harga per area
- Area & tarif ongkir
- Kelola customer
- Approve order + estimasi cuan/rugi
- Sistem referal
- Laporan

### 9. Panel Customer
- Katalog produk + harga sesuai area
- Buat order + input tujuan kirim
- Total otomatis (produk + ongkir)
- Riwayat order & status
- Monitoring pengiriman
- Sistem referal

### 10. Panel Sales (Monitoring)
- Dashboard ringkasan area sendiri
- Monitoring transaksi customer di area (read-only)
- Lacak customer referal
- Riwayat transaksi area

### 11. Fitur Awal (Fase 1)
- Login Gmail + manual (bisa buat user dua-duanya)
- 4 role + dashboard
- Katalog & harga per area
- Order online dengan estimasi ongkir
- Estimasi cuan/rugi saat approve
- Sistem referal
- Approve order oleh admin
- Monitoring status semua panel
- Laporan penjualan dasar

### 12. Upgrade Level 1 — Business
- Analisa transaksi (tren & produk terlaris)
- Piutang & kredit (limit, aging, status bayar)
- Target & komisi sales
- Laporan lengkap (Excel/PDF) + audit trail

### 13. Upgrade Level 2 — Enterprise
- Integrasi produsen (sinkron harga & PO)
- Tracking pengiriman real-time
- Multi-gudang & manajemen stok

### 14. Teknologi & Keamanan
- Aplikasi web modern, jalan di browser (HP/laptop/PC)
- Login aman (Google + manual)
- Data tersimpan terpusat, akses per role
- Bahasa Indonesia

### 15. Proses Implementasi
- Konsultasi kebutuhan
- Setup & konfigurasi
- Import data awal (produk, customer, harga)
- Pelatihan admin/sales/customer
- Go live + pendampingan

### 16. Paket & Biaya
- Biaya setup (sekali)
- Langganan bulanan (berisi fitur Fase 1)
- Upgrade level 1 & 2 (tambahan)
- (isi harga sesuai kesepakatan)

### 17. Penutup / CTA
- "Siap tingkatkan operasional distributor Anda?"
- Undang diskusi, demo, atau tanya harga
- Kontak JR Logix
- Terima kasih

---

## B. Prompt untuk NotebookLM

```
Kamu adalah desainer slide profesional. Buatkan presentasi PowerPoint (16:9) untuk
proposal penawaran aplikasi bernama "DistributorApp" dari pengembang "JR Logix"
kepada calon client distributor semen.

TEMA DESAIN
- Profesional, clean, modern. Latar terang/putih-keabu. JANGAN gelap.
- Palet warna (gunakan ini):
  - Merah primary: #D62828
  - Kuning aksen: #FFB703
  - Latar: #F8F9FA, putih #FFFFFF
  - Teks: #1A1A1A, abu teks sekunder #6B7280
  - Border: #E2E5E9
  - Hijau sukses: #10B981
- Font heading: Outfit (atau font sans-serif tebal). Font body: Plus Jakarta Sans
  (atau sans-serif bersih). Konsisten di semua slide.
- Hindari elemen klise/berlebihan. Pakai spacing lega, judul besar,
  bullet ringkas. Satu slide = satu pesan utama.

ATURAN ISI
- Jangan menyebut nama produsen/merk semen tertentu. Cukup kata "produsen".
- Bahasa Indonesia, profesional, hemat kata.
- Jangan buat klaim angka/omset yang tidak ada di materi.

STRUKTUR (ikuti persis 17 bagian ini):
1. Cover — judul DistributorApp, subjudul "Aplikasi Manajemen Distributor Semen —
   Order Online, Estimasi Ongkir, & Monitoring Penjualan", footer "Proposal
   Penawaran — JR Logix".
2. Agenda
3. Tentang JR Logix
4. Masalah di Bisnis Distributor
5. Solusi: DistributorApp
6. Alur Bisnis (diagram 6 langkah panah)
7. Panel & Role
8. Panel Admin
9. Panel Customer
10. Panel Sales (Monitoring)
11. Fitur Awal (Fase 1)
12. Upgrade Level 1 — Business
13. Upgrade Level 2 — Enterprise
14. Teknologi & Keamanan
15. Proses Implementasi
16. Paket & Biaya (dengan placeholder harga)
17. Penutup / CTA

LAYOUT PER SLIDE
- Slide 1: judul besar center-left, garis aksen merah, subjudul, footer kecil.
- Slide 4 & 5: 2 kolom (kiri masalah / kanan solusi) atau grid kartu.
- Slide 6: 6 kotak berurutan dengan panah.
- Slide 7: 4 kartu (Owner, Admin, Sales, Customer).
- Slide 8-10: kartu per modul, rapikan dalam grid.
- Slide 11-13: grid kartu fitur; Level 1 & 2 bisa di satu slide dua kartu besar.
- Slide 15-16: urutan step / tabel harga yang rapi.
- Setiap slide punya header: judul + satu baris subtitle.

HASIL AKHIR
- Ekspor/kompilasi menjadi file .pptx yang siap diedit di PowerPoint.
- Pastikan semua teks terbaca, tidak ada elemen saling menutupi,
  dan tampil konsisten 16:9.
```

---

## C. Cara Pakai di NotebookLM

1. Buka https://notebooklm.google.com → buat notebook baru.
2. Upload sumber: salin **Bagian A (Kerangka)** ke catatan/notebook.
3. Paste **Bagian B (Prompt)** di kolom chat NotebookLM.
4. Minta hasil dalam bentuk PowerPoint / slide.
5. Export file, lalu cek & rapikan di PowerPoint (font/palette disesuaikan otomatis oleh NotebookLM, cek ulang).
