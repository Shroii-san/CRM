# ROADMAP & FASE IMPLEMENTASI

Dokumen ini berisi urutan pengerjaan refactoring dan pengembangan sistem berdasarkan ketergantungan antar-lapisan (_dependency ladder_), serta pembagian tingkat prioritas tugas.

---

## URUTAN IMPLEMENTASI (PHASING)

### PHASE 1: DOMAIN & DATABASE DESIGN

> **Definition of Done:** Arsitektur ERD, relasi tabel, dan aturan domain terdefinisi secara jelas di dokumen teknis sebelum eksekusi skema.

- [x] **Entity & Attribute Mapping** (Company, Customer, Contact/PIC, Sales, Deal, Pipeline, Stage)
- [x] **Relationship Design** (Foreign keys, cascade rules, referential integrity)
- [x] **Pipeline & Stage Business Logic** (Aturan transisi stage & lifecycle deal)
- [x] **Attachment Architecture** (Tabel metadata & pola penyimpanan file)
- [x] **Timeline & Audit Trail Design** (Skema pencatatan riwayat aktivitas & perubahan data)

---

### PHASE 2: DATABASE MIGRATION & SEEDER

> **Definition of Done:** Database dapat di-recreate dari nol hanya dengan menjalankan `php artisan migrate:fresh --seed` tanpa error.

- [x] **Migration Scripts** (Seluruh tabel utama, pivot, dan foreign key constraints)
- [x] **Master Data Seeders** (Data wilayah Indonesia, default pipeline & stage, role/permission)
- [ ] **Dummy Data Seeders** (Data simulasi untuk kebutuhan testing pengembangan)

---

### PHASE 3: BACKEND FOUNDATION

> **Definition of Done:** Terbentuknya struktur kode backend yang composable, terpisah responsibility-nya, dan memiliki standar response API yang konsisten.

- [ ] **Model & Relationships** (Setup Eloquent model, fillable, casts, & relationships)
- [ ] **Form Requests** (Validasi terpusat untuk create/update pada setiap resource)
- [ ] **Business Logic / Services** (Pemisahan logic dari Controller ke Service layer)
- [ ] **Centralized Region API** (Endpoint terpusat Provinsi -> Kota -> Kec -> Kel)
- [ ] **Standard Query Pattern** (Composable Search, Filter, Sort, & Pagination)
- [ ] **AJAX Response Standardizer** (Format JSON `success`, `message`, `data`, `errors`)
- [ ] **Import & Export Handlers** (Validasi file, parsing data, & penanganan error import)

---

### PHASE 4: PIPELINE & DEAL CORE ENGINE

> **Definition of Done:** Fitur inti pipeline dapat berjalan dinamis dengan validasi transisi stage dan pencatatan riwayat perubahan.

- [ ] **Dynamic Pipeline & Stage CRUD**
- [ ] **Stage Transition Rules & Validation** (Mencegah perpindahan stage yang tidak valid)
- [ ] **Drag & Drop API Integration** (Endpoint penyesuaian posisi/stage deal)
- [ ] **Stage History Tracker** (Pencatatan otomatis setiap terjadi perpindahan stage)
- [ ] **Deal Timeline Aggregator** (Mengumpulkan seluruh aktivitas deal dalam satu feed)

---

### PHASE 5: FRONTEND ARCHITECTURE & COMPONENTS

> **Definition of Done:** Seluruh komponen UI dasar tersedia dalam bentuk modular/reusable dan tidak ada lagi inline JS/CSS liar di Blade.

- [ ] **Vite Asset Bundling** (Migrasi seluruh skrip dari `public/` ke `resources/`)
- [ ] **JS Modularization** (Pemisahan berkas JS berdasarkan responsibility)
- [ ] **Reusable DataTable Component** (Mendukung AJAX search, filter, sort, & pagination)
- [ ] **Reusable Modal & Confirmation Modal** (Menggantikan native `alert()` & `confirm()`)
- [ ] **Reusable Cascade Select Component** (Untuk dropdown wilayah berantai)
- [ ] **Reusable Chart Component** (Standardisasi rendering & fetching data chart)
- [ ] **AJAX State Manager** (Standardisasi tampilan Loading, Success, Empty, & Error)

---

### PHASE 6: PAGE IMPLEMENTATION

> **Definition of Done:** Halaman UI terintegrasi penuh dengan backend foundation & komponen reusable.

- [ ] **Modul Customer** (Bulk action, pagination, modal PIC/Company)
- [ ] **Modul Company** (Table, detail modal, cascade location, dedicated form)
- [ ] **Modul Sales Management** (Role mapping, user integration, detail follow-up)
- [ ] **Modul Sales Visit** (Location auto-fill, dedicated modal, export/import)
- [ ] **Modul Deal Management** (Form lifecycle, bukti attachment, KPI separation)
- [ ] **Modul Pipeline Board** (Tampilan kanban drag & drop, UX peek/scroll fix)
- [ ] **Modul Dashboard** (KPI widgets, activity feeds, chart components)
- [ ] **Modul Navbar & Sidebar** (Pemisahan responsibilitas, scrolling fix, dynamic notification)
- [ ] **Modul Menu Management** (Resource-based create, drag & drop order, icon picker)

---

### PHASE 7: POLISHING & PRODUCTION READINESS

> **Definition of Done:** Aplikasi bersih dari instruksi debug, responsif, aman, dan siap digunakan di lingkungan produksi.

- [ ] **UX & Responsiveness Audit** (Pengujian layout pada berbagai ukuran layar)
- [ ] **Accessibility & Terminology Pass** (Penyelarasan istilah bisnis di seluruh UI)
- [ ] **State Handling Coverage** (Memastikan seluruh komponen AJAX punya handling empty/error)
- [ ] **Tailwind Production Build** (Purge CSS & minify JS via Vite)
- [ ] **Code Cleanup** (Hapus `console.log`, file obsolete, & inline style)
- [ ] **Security Review** (Enforce backend authorization, CSRF, XSS, & file upload check)
- [ ] **Performance Review** (Optimasi query N+1, indexing database, & AJAX response efficiency)

---

## RINGKASAN PRIORITAS (SLA)

| Level  | Kategori                  | Fokus Utama                                                                                                                                                            |
| :----- | :------------------------ | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **P0** | **Fundamental / Fatal**   | Database/domain model, Business logic Pipeline & Stage, Lifecycle Deal, Relasi Company-Customer-PIC-Sales, Stage History, Timeline, Attachment architecture.           |
| **P1** | **Backend Architecture**  | Separation of concern, Region terpusat, Form Request, Query pattern (search/filter/sort/page), Standarisasi JSON response, DB Transaction, Import/Export, Soft delete. |
| **P2** | **Frontend Architecture** | Vite build setup, JS modularization, Reusable DataTable/Modal/ConfirmModal/Chart/CascadeSelect, Handling state (Loading/Empty/Error).                                  |
| **P3** | **Feature Completion**    | Refactoring per halaman (Customer, Company, Sales Management, Sales Visit, Deal, Pipeline, Dashboard, Navbar/Sidebar, Menu Management).                                |
| **P4** | **Polish**                | UX, Responsive, Accessibility, Konsistensi istilah/ikon, Cleanup code, Tailwind build optimization, Security & Performance audit.                                      |
