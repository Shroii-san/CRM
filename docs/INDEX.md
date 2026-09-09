# INDEKS DOKUMENTASI SISTEM

Selamat datang di direktori dokumentasi proyek. Seluruh dokumen di dalam folder `docs/` ini disusun untuk menjadi acuan teknis dalam proses _refactoring_, pengembangan fitur baru, serta pemeliharaan sistem.

---

## 📚 DAFTAR DOKUMEN

### 1. [01 - Laporan Audit Sistem](./01-system-audit.md)

Dokumen analisis mendalam mengenai kondisi basis kode (_legacy code_). Berisi temuan 27 poin masalah yang mencakup kelemahan UI/UX, arsitektur _backend_, _domain model_, hingga masalah fatal pada _database_ dan _pipeline logic_.

### 2. [02 - Roadmap & Fase Implementasi](./02-roadmap-and-phases.md)

Panduan alur pengerjaan _refactoring_ berbasis ketergantungan (_dependency ladder_). Berisi pembagian 7 Fase pengembangan, kriteria _Definition of Done_ (DoD), serta pengelompokan prioritas tugas dari **P0** (Fatal) hingga **P4** (Polish).

### 3. [03 - Spesifikasi Domain & Database](./03-domain-and-database.md) _(Dalam Penyusunan)_

Cetak biru (_blueprint_) dan _source of truth_ rekonstruksi _database_. Berisi peta relasi entitas (_Company_, _Customer_, _PIC_, _Sales_, _Deal_, _Pipeline_, _Stage_), spesifikasi kolom tabel, serta skema pendukung (_stage history_, _attachments_, dan _audit log_).

### 4. [04 - Arsitektur Backend & Standar API](./04-architecture-and-api.md)

Aturan teknis pengembangan _backend_ menggunakan Laravel. Berisi standar pemisahan logika (_Separation of Concerns_), penggunaan _Form Request_, format baku respons JSON untuk AJAX (`success` & `error`), _composable query pattern_ (search/filter/sort/pagination), API wilayah terpusat, serta keamanan otorisasi.

### 5. [05 - Arsitektur Frontend & Reusable Components](./05-ui-and-components.md)

Aturan teknis pengembangan antarmuka. Berisi manajemen aset Vite (`resources/` vs `public/`), strategi _hybrid rendering_, modularisasi JavaScript, panduan pembuatan komponen _reusable_ (DataTable, ConfirmModal, Cascade Select), serta manajemen 4 _state_ AJAX (Loading, Success, Empty, Error).

---

## 🔗 LINK CEPAT

- 📄 [Kembali ke README Utama (Root Project)](../README.md)

---

## 💡 PANDUAN PENGGUNAAN DOKUMENTASI

1. **Developer Baru:** Dimulai dari membaca `README.md` untuk setup environment lokal, lalu pelajari `04-architecture-and-api.md` dan `05-ui-and-components.md` sebelum menulis kode.
2. **Pelaporan Progress:** Gunakan checklist pada `02-roadmap-and-phases.md` untuk memantau status pengerjaan fitur yang sedang berjalan.
3. **Penambahan Fitur Baru:** Setiap perubahan skema database atau penambahan komponen UI wajib memperbarui dokumen `03` dan `05` agar dokumentasi tetap relevan.
