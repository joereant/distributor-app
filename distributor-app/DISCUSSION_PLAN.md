# DistributorApp — Discussion Plan

## Tujuan
Menyepakati semua keputusan teknis & bisnis sebelum mulai build aplikasi.

---

## 1. Definisi Project
- [ ] Nama project final
- [ ] Deskripsi singkat (1-2 paragraf)
- [ ] Target pengguna (siapa yang pakai?)
- [ ] Goal utama (apa yang harus bisa dilakukan?)

## 2. Tech Stack
- [ ] Backend: Laravel 13 (konfirmasi versi)
- [ ] Frontend: Svelte 5 + Inertia.js
- [ ] Styling: Tailwind CSS
- [ ] Database: PostgreSQL
- [ ] Auth: Azure SSO / Google OAuth / Manual?
- [ ] Deployment: Docker / Laravel Sail / VPS?
- [ ] Version control: Git (branch strategy?)

## 3. Panel & Role
- [ ] Panel apa saja yang dibutuhkan?
  - [ ] Admin
  - [ ] Sales
  - [ ] Customer
  - [ ] VIP
- [ ] Apa saja yang bisa dilakukan tiap panel?
- [ ] Role bisa nambah di tengah development? (Ya/Tidak)
- [ ] Bagaimana cara nambah role baru?

## 4. Model Data
- [ ] Daftar model utama (user, customer, product, transaction, dll)
- [ ] Relasi antar model
- [ ] Field apa saja untuk tiap model?
- [ ] Apakah ada modul tambahan nanti? (inventory, laporan, dll)

## 5. Auth & Verifikasi
- [ ] Metode auth (Azure SSO, Google OAuth, manual)
- [ ] Flow registrasi (manual + verifikasi admin?)
- [ ] Role assignment saat registrasi
- [ ] Reset password & lupa akun

## 6. Fitur Inti (MVP)
- [ ] Daftar fitur yang masuk MVP
- [ ] Prioritas fitur (P0, P1, P2)
- [ ] Apa yang tidak masuk MVP?

## 7. Demo Plan
- [ ] Fitur apa yang ditampilkan di demo?
- [ ] Siapa audience demo-nya?
- [ ] Platform demo (local / staging / deploy?)
- [ ] Timeline demo

## 8. Desain UI/UX
- [ ] Style guide (warnanya apa?)
- [ ] Layout pattern (ikut Corsales atau custom?)
- [ ] Responsive design (mobile-first?)
- [ ] Icon library

## 9. Deployment & Infrastructure
- [ ] Environment (dev, staging, prod)
- [ ] Hosting (VPS, Herd, Docker?)
- [ ] CI/CD (auto-deploy?)
- [ ] Backup strategy

## 10. Timeline & Milestones
- [ ] Kapan demo pertama?
- [ ] Kapan MVP selesai?
- [ ] Kapan production ready?
- [ ] Siapa yang ngapain (dev, design, client)?

## 11. Guard Rails
- [ ] Apa yang TIDAK boleh diubah tanpa diskusi?
- [ ] Batasan anggaran/waktu?
- [ ] Stakeholder yang harus approve?

---

*Status: Belum disepakati — diskusi belum dimulai.*