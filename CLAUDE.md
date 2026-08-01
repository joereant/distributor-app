# DistributorApp — CLAUDE.md

**WAJIB — BACA SEBELUM BACA APA PUN:**
Saat user bilang "mulai sesi" / "mulai" / "kita mulai" / "gas" / "lanjut":
→ JALANKAN SOP Mulai Sesi di bawah. JANGAN sapa-sapa.

Saat user bilang "proses inbox" / "olah inbox":
→ JALANKAN Siklus Proses Inbox dari `../ObsidianDen/99_System/agents.md` section 3.

Saat user bilang "bungkus" / "selesai":
→ JALANKAN SOP Bungkus di bawah.

Saat user bilang "setup project [nama]" / "buat project [nama]":
→ JALANKAN SOP Setup Project Baru dari `../ObsidianDen/99_System/SETUP_STANDARD.md` section 3.

Saat user bilang "handoff" / "handoff ke [agent]" / "serahin ke [agent]":
→ JALANKAN SOP Handoff di bawah.

⚠️ Agent WAJIB auto-detect kapan musti handoff — jangan nunggu om bilang.
   Baca `../ObsidianDen/99_System/handoffs/README.md` → section "Kapan Agent HARUS Otomatis Handoff"

---

## Konteks
DistributorApp — Aplikasi manajemen distributor semen (user management, transaksi penjualan, monitoring).
Stack: Laravel 13 + Svelte 5 + Inertia + Tailwind + PostgreSQL.
Kode: `distributor-app/` (subfolder Laravel).

## Agent Registry
Lihat `../ObsidianDen/99_System/agents.md` §0 — **6 agent aktif** (opencode, Kilo, Claude, Bluepack, Agy, Agent/Cursor).
Task routing & handoff matrix ada di sana. Jangan assume cuma 3 agent.

## Second Brain (ObsidianDen)
Semua konteks global ada di `../ObsidianDen/99_System/`.

**WAJIB baca tiap sesi (hemat token):**
- `../ObsidianDen/99_System/ctx/CURRENT.md` — PERTAMA: konteks + prioritas + handoff
- `../ObsidianDen/99_System/SESSION_INIT.md` — device, infra, projects
- `../ObsidianDen/99_System/skills/index.md` — metadata skills (~30 baris)
- `../ObsidianDen/99_System/handoffs/REGISTRY.md` — cek handoff pending
- `../ObsidianDen/99_System/session-state.jsonl` — 10 baris terakhir

**On demand:**
- `BLUEPRINT.md` / `agents.md` / `SETUP_STANDARD.md` — arsitektur, full rules, setup
- `handoffs/README.md` — kalo ada handoff aktif
- `../ObsidianDen/10_Projects/DistributorApp/DECISIONS.md` — keputusan & guard rails

## SOP Mulai Sesi
```
1. Cek status 2 repo:
   git status                        → project
   git -C ../ObsidianDen status      → ObsidianDen
   → kalo ada pending: LAPOR om + tanya commit/skip. JANGAN LANJUT sebelum dijawab.

2. Pull:
   git -C ../ObsidianDen pull
   git pull

3. Baca konteks (urut, hemat token):
   ../ObsidianDen/99_System/ctx/CURRENT.md    ← PERTAMA
   ../ObsidianDen/99_System/SESSION_INIT.md
   ../ObsidianDen/99_System/skills/index.md
   ../ObsidianDen/99_System/handoffs/REGISTRY.md
   (10 baris terakhir session-state.jsonl)
   CLAUDE.md (ini)
   → BLUEPRINT/agents/SETUP hanya on demand

4. Cek CURRENT.md field "Handoff":
   → Baca field "Project Tag" di CURRENT.md
   → Baca field "project" di handoff packet
   → Jika SAMA → JALANKAN SOP Terima Handoff
   → Jika BEDA / "none" → ABAIKAN, lanjut kerja
5. Kalo gak ada handoff → lanjut kerja
```

## SOP Handoff
Gunakan ketika:
1. Om bilang "handoff"
2. **Auto-detect:** agent sadar kudu handoff (token abis, beda tool, blocker, butuh specialist)
3. "Bungkus" tapi masih ada pending task

Auto-detect berjalan TERUS selama sesi — cek list trigger di `../ObsidianDen/99_System/handoffs/README.md`.

```
1. Cek working tree: git status + git -C ../ObsidianDen status → musti CLEAN
   Kalo ada uncommitted: commit dulu

2. Baca handoff protocol dulu:
   ../ObsidianDen/99_System/handoffs/README.md

3. Bikin handoff packet:
   - Copy ../ObsidianDen/99_System/handoffs/TEMPLATE.md → ../ObsidianDen/99_System/handoffs/hnd_<next-id>.md
   - Isi: from_agent, to_agent, project, task, progress summary, next steps
   - Status: "pending"

4. Daftarin di REGISTRY.md:
   - Tambah baris di tabel Active

5. Update ctx/CURRENT.md:
   - Status → "🟡 Handoff: pending → [agent penerima]"
   - Handoff → "hnd_[id] → [agent]"
   - Project Tag → [project yg sama dengan di packet]

6. Commit + push ObsidianDen:
   git -C ../ObsidianDen add -A
   git -C ../ObsidianDen commit -m "handoff: hnd_[id] → [agent] buat [task]"
   git -C ../ObsidianDen push

7. Lapor om: "Handoff ke [agent] — task: [task] — cek hnd_[id].md"
```

## SOP Bungkus
```
1. Kalo masih ada pending task → jalankan SOP Handoff DULU
2. Tulis session_end ke ../ObsidianDen/99_System/session-state.jsonl
3. Update ../ObsidianDen/99_System/ctx/CURRENT.md — overwrite dengan konteks sesi ini
   (prioritas aktif, keputusan, guard rails sesi ini)
4. Catat insight/decision di ../ObsidianDen/10_Projects/DistributorApp/DECISIONS.md
   Format wajib: YAML frontmatter + tabel Keputusan (Tanggal|Keputusan|Alasan) + Guard Rails + Insight Sesi
5. Update INDEX.md + LOG.md (kalau ada perubahan vault)
6. Push ObsidianDen: git -C ../ObsidianDen add -A && git -C ../ObsidianDen commit -m "..." && git -C ../ObsidianDen push
7. Push project: git add -A && git commit -m "..." && git push
8. Verifikasi status (2 repo musti clean)
9. Lapor om
```

## SOP Terima Handoff
Jalankan otomatis saat baca ctx/CURRENT.md dan liat ada handoff pending.

```
1. Catat handoff_id dari ctx/CURRENT.md
2. Baca packet: ../ObsidianDen/99_System/handoffs/hnd_[id].md
3. COCOKKAN project_tag:
   - Cek "Project Tag" di ctx/CURRENT.md
   - Cek "project" di handoff packet
   - Kalo BEDA → SKIP. Handoff bukan untuk sesi ini.
   - Kalo SAMA → lanjut step 4
4. Kalo sanggup → update status packet ke "accepted"
   Kalo gak sanggup → update ke "rejected" + alasan, kasih tau om
5. Update REGISTRY.md kolom status
6. Kerjain task sesuai packet
7. Kalo selesai → update packet status ke "completed"
8. Kalo gagal → update ke "failed" + catet alasannya
```

## Coding Guidelines
### 1. Pikir Dulu Sebelum Koding (Strict Text-First Protocol)
- **STRICT TEXT-FIRST PROTOCOL**: DILARANG memanggil tool edit file atau run_command eksekusi sebelum memaparkan analisis & rencana di chat teks + menunggu konfirmasi "gas" dari om. JANGAN main gas-gas sendiri!
- **HARD LOCK DEPLOY PROD**: DILARANG keras memanggil command deploy / SSH prod di jam kerja (siang hari) tanpa izin eksplisit om.
- Nyatakan asumsi & tanya jika ragu. Sajikan pilihan.
- Usulkan pendekatan tersimpel. Push back kalo ada yg lebih baik.

### 2. Simplicity First
- Kode minimal. No speculative code, no extra features.
- Dilarang abstraksi untuk single-use code.

### 3. Surgical Changes
- Edit HANYA yang perlu. Jangan refactor kode gak berkaitan.
- Hapus import/var yang jadi unused akibat perubahanmu.

### 4. Goal-Driven Execution
- Task → kriteria sukses terverifikasi. Plan dulu, verify per step.

## Git Rules
- **1 branch: `main`**.
- **Manual push via "bungkus"** — jangan auto-push.
- 2 repo terpisah. Push KEDUANYA.
- Kalo conflict → STOP, tanya om.

## Anti-Drift
- SESSION_INIT.md = source of truth. Jangan override.
- Wajib verifikasi sebelum klaim "done"/"berhasil".
- Gak yakin? Tandai `UNVERIFIED`. Tanya om.

## Peta Dokumen
| File | Lokasi | Isi |
|------|--------|-----|
| `CLAUDE.md` | `./` | Aturan + entrypoint (ini) |
| `TECH_SPECS.md` | `./` | Tech Specs & arsitektur |
| `DECISIONS.md` | `../ObsidianDen/10_Projects/DistributorApp/` | Keputusan & guard rails |
| `CURRENT.md` | `../ObsidianDen/99_System/ctx/` | Context Package — konteks sesi terakhir |
| `README.md` | `../ObsidianDen/99_System/handoffs/` | Handoff Protocol — cara handoff antar-agent |
| `REGISTRY.md` | `../ObsidianDen/99_System/handoffs/` | Daftar handoff aktif & riwayat |
| `SESSION_INIT.md` | `../ObsidianDen/99_System/` | Konteks global lintas project |
| `BLUEPRINT.md` | `../ObsidianDen/99_System/` | Arsitektur vault v3 (multi-agent) |
| `agents.md` | `../ObsidianDen/99_System/` | Aturan kerja AI agent + task-to-agent mapping + handoff decision matrix |
| `skills/index.md` | `../ObsidianDen/99_System/skills/` | Metadata skills (always load) |
| `session-state.jsonl` | `../ObsidianDen/99_System/` | Event log resume antar-sesi |
| `SETUP_STANDARD.md` | `../ObsidianDen/99_System/` | SOP git & standarisasi |