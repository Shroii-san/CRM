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
git clone https://github.com/Shroii-san/CRM.git
cd CRM
```

### 2. Install Dependensi Backend & Frontend

```bash
# Install dependensi PHP
composer install

# Install dependensi JavaScript
npm install

# Perbaiki jika ada vulnerability dependensi JavaScript
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
DB_PASSWORD=password_database_lokal
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Pembuatan database (jika database belum dibuat manual di komputer lokal)

```bash
php artisan db:create
```

### 6. Jalankan Migration dan Seeder Database

Pastikan PostgreSQL sudah berjalan dan database crm_db sudah dibuat, lalu jalankan migration untuk membuat tabel-tabel database serta seeder untuk mengisi data tabel:

```bash
php artisan migrate:fresh --seed
```

Atau jalankan untuk membuat database sekaligus schema tabel beserta data-data nya :

```bash
php artisan db:create && php artisan migrate:fresh --seed
```

**PENTING:** dengan melakukan seeder akan mereset data yang ada ke data default

### 7. Jalankan Server

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
- [02 - Roadmap & Fase Implementasi](./docs/02-roadmap-phases.md): Urutan pengerjaan berbasis dependency dan berdasarkan prioritas (P0–P4).
- [03 - Spesifikasi Domain & Database](./docs/03-domain-database.md): Cetak biru skema database dan relasi entitas baru.
- [04 - Arsitektur Backend & API](./docs/04-architecture-api.md): Standar Form Request, JSON response, Service Layer, dan Query pattern.
- [05 - Arsitektur Frontend & Components](./docs/05-ui-components.md): Standar Vite, AJAX state handling, dan komponen reusable.

---

## STANDAR KONTRIBUSI & COMMIT

Untuk menjaga kerapian riwayat Git, gunakan format pesan commit berbasis konvensi (Conventional Commits):

- `feat:` untuk penambahan fitur baru (misal: `feat: implementasi cascade select wilayah`).
- `fix:` untuk perbaikan bug (misal: `fix: perbaiki pagination modal customer`).
- `refactor:` untuk perubahan kode tanpa mengubah fungsionalitas (misal: `refactor: pisahkan logic controller ke service`).
- `docs:` untuk perubahan pada file dokumentasi.
