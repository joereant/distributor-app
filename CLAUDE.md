# DistributorApp — CLAUDE.md

## Trigger Cepat
- **Mulai sesi:** Cek `git status` 2 repo (distributor-app + ObsidianDen), `git pull`. Baca `../ObsidianDen/99_System/ctx/CURRENT.md`. Langsung kerja / tanya om (JANGAN salam/basa-basi).
- **Bungkus:** Update `CURRENT.md` + `DECISIONS.md`, commit & push 2 repo, lapor om.
- **Handoff / Aturan Global:** Ikuti `../ObsidianDen/99_System/agent-registry.md`. File sistem lain (`SESSION_INIT`, `skills/`, dll) HANYA dibaca on-demand jika dibutuhkan.

---

## Konteks & Stack
- **Project:** DistributorApp — Aplikasi manajemen distributor semen (user management, transaksi penjualan, monitoring).
- **Stack:** Laravel 13 + Svelte 5 + Inertia + Tailwind + PostgreSQL.
- **Path Kode:** `distributor-app/`

## Cara Menjalankan (Run & Dev)
- **Backend:** `php artisan serve` (dari `distributor-app/`)
- **Frontend:** `npm run dev` (dari `distributor-app/`)
- **Test:** `php artisan test`

## Project Guard Rails
1. **Strict Text-First Protocol:** DILARANG memanggil tool edit file atau run_command eksekusi sebelum memaparkan analisis & rencana di chat teks + menunggu konfirmasi "gas" dari om.
2. **Hard Lock Deploy Prod:** DILARANG keras memanggil command deploy / SSH prod di jam kerja (siang hari) tanpa izin eksplisit om.
3. **Surgical Changes:** Sentuh HANYA yang perlu. Hapus unused import/var.

## Peta Dokumen (On-Demand)
- Keputusan & Guard Rails: `../ObsidianDen/10_Projects/DistributorApp/DECISIONS.md`
- Tech Specs: `./TECH_SPECS.md`
- Aturan Global: `../ObsidianDen/99_System/agent-registry.md`