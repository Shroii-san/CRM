# PROYEK PENGEMBANGAN SISTEM (REFACTORING)

## Proyek ini kelanjutan dari pengembangan sistem manajemen penjualan/CRM

## TECH STACK

- **Backend Framework:** Laravel (PHP 8.2+)
- **Frontend Framework / Styling:** Blade, Tailwind CSS, Native JS
- **Asset Bundler:** Vite
- **Database:** PostgreSQL

---

## PANDUAN SETUP LOKAL

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda:

### 1. Clone Repositori

```bash
git clone <URL_REPOSITORY_KAMU>
cd <NAMA_FOLDER_PROYEK>
```

### 2. Install Dependensi Backend & Frontend

```bash
# Install dependensi PHP
composer install

# Install dependensi JavaScript
npm install

# Perbaiki jika ada vulnerability dependensi javascript
npm audit fix
```

### 3. Konfigurasi Environment (`.env`)

Salin file `.env.example` menjadi `.env`, lalu atur konfigurasi database.

```bash
cp .env.example .env
```

Atur baris berikut di file `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=crm_db
DB_USERNAME=postgres
DB_PASSWORD=password_database
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Server Pengembangan

Jalankan server Laravel dan bundler Vite secara bersamaan (bisa gunakan 2 tab terminal terpisah):

```bash
# Terminal 1: Server Laravel
php artisan serve

# Terminal 2: Vite Assets Bundler (Development)
npm run dev
```

Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## DOKUMENTASI LENGKAP PROYEK

Seluruh dokumentasi teknis, hasil audit sistem, arsitektur backend/frontend, serta roadmap pengerjaan tersimpan secara terstruktur di dalam folder `docs/`.

Silakan buka [Dokumentasi Indeks (`docs/INDEX.md`)](./docs/INDEX.md) untuk membaca spesifikasi lengkap:

- [01 - Laporan Audit Sistem](./docs/01-system-audit.md): Analisis 27 temuan masalah pada kode terdahulu.
- [02 - Roadmap & Fase Implementasi](./docs/02-roadmap-and-phases.md): Urutan pengerjaan berbasis dependency dan prioritas SLA (P0–P4).
- [03 - Spesifikasi Domain & Database](./docs/03-domain-and-database.md): Cetak biru skema database dan relasi entitas baru.
- [04 - Arsitektur Backend & API](./docs/04-architecture-and-api.md): Standar Form Request, JSON response, Service Layer, dan Query pattern.
- [05 - Arsitektur Frontend & Components](./docs/05-ui-and-components.md): Standar Vite, AJAX state handling, dan komponen reusable.

---

## STANDAR KONTRIBUSI & COMMIT

Untuk menjaga kerapian riwayat Git, gunakan format pesan commit berbasis konvensi (Conventional Commits):

- `feat:` untuk penambahan fitur baru (misal: `feat: implementasi cascade select wilayah`).
- `fix:` untuk perbaikan bug (misal: `fix: perbaiki pagination modal customer`).
- `refactor:` untuk perubahan kode tanpa mengubah fungsionalitas (misal: `refactor: pisahkan logic controller ke service`).
- `docs:` untuk perubahan pada file dokumentasi.
