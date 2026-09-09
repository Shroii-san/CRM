# ANALISIS DAN RENCANA PERBAIKAN SISTEM

## 1. NAVBAR & SIDEBAR

### 1.1 Navbar

- Data notifikasi masih hardcoded.
- Belum ada halaman khusus untuk menampilkan seluruh notifikasi.
- File navbar dan sidebar masih menyatu sehingga responsibility belum terpisah.
- Data notifikasi harus bersumber dari database.
- Perlu mekanisme status notifikasi, minimal:
    - unread
    - read

- Notifikasi juga sebaiknya memiliki timestamp dan referensi terhadap aksi yang dilakukan.

### 1.2 Sidebar

- Tombol logout dapat menutupi menu ketika posisinya berada setelah menu yang memiliki child.
- Struktur sidebar perlu diperbaiki agar area menu dapat melakukan scrolling tanpa membuat tombol logout bertabrakan dengan menu.
- Menu parent/child perlu memiliki struktur layout yang konsisten.
- State menu seperti expanded/collapsed perlu dipisahkan dari positioning tombol logout.

---

# 2. DASHBOARD

## 2.1 Chart

- File asset/source `public/js` dan `public/css` yang seharusnya dikelola Vite dipindahkan ke `resources/js` dan `resources/css`.
- `public/` tetap digunakan untuk asset statis yang memang tidak melalui proses build.
- Pisahkan controller berdasarkan responsibility, bukan semata-mata berdasarkan jenis visual.
- `DashboardController` digunakan untuk kebutuhan halaman/dashboard.
- Endpoint data dashboard dapat dipisahkan berdasarkan kebutuhan data, misalnya KPI, performance, activity, dan sebagainya.
- Hindari membuat satu `ChartController` yang hanya menjadi kumpulan method untuk setiap chart apabila method tersebut sebenarnya mewakili business/query concern yang berbeda.
- Buat component chart reusable.

Contoh konsep:

```text
Chart
├── id
├── type
├── title
└── endpoint
```

Component bertanggung jawab terhadap:

- fetch data
- loading state
- success state
- empty state
- error state
- rendering chart

Endpoint bertanggung jawab terhadap penyediaan data.

---

## 2.2 Dynamic Page

Data yang masih static harus diubah menjadi data dari database.

Format:

```text
section / bagian
└── nama file
```

### KPI

- `kpi`

### Follow-up Reminder

- `folre`

Sebaiknya diubah menjadi :

- `reminder`

### Komunikasi

- `komunikasi`

### Performa Wilayah

- `performa`

### Aktivitas Terbaru

- `aktivitasterbaru`

Sebaiknya diubah menjadi :

- `aktivitas-terbaru`

### Quick Action & Schedule

- `action`

Namun Quick Action dan Schedule sebaiknya dipisahkan menjadi:

```text
quick-action
schedule
```

Semua data dashboard harus memiliki state:

```text
loading
success
empty
error
```

---

# 3. CUSTOMER

## 3.1 Table

- Checklist dapat memilih banyak data, tetapi action yang tersedia hanya bekerja pada satu row.
- Karena checklist sudah diimplementasikan, maka perbarui sehingga bisa bulk action.
- Contoh bulk action adalah:
    - delete selected
    - export selected
    - change status selected

- Pagination belum bekerja.
- Belum tersedia sorting `ASC/DESC`.
- Belum dapat menentukan custom `ORDER BY`.
- Search, filter, sort, dan pagination harus menggunakan pola yang lebih baik sehingga tidak saling merusak.

Contoh:

```text
search
↓
filter
↓
sort
↓
pagination
```

Request dapat menggunakan pola:

```text
/customers
    ?search=andi
    &status=active
    &sort=name
    &direction=asc
    &page=2
```

---

## 3.2 Action Modal

### Cascade Location

- Cascade dropdown location gagal melakukan fetch data.
- Logic cascade location harus menggunakan endpoint terpusat sehingga tidak terjadi duplikasi method pada setiap controller.
- Struktur:

```text
Province
   ↓
Regency / City
   ↓
District
   ↓
Village
```

### PIC

- Option PIC masih hardcoded.
- PIC/Contact Person harus berasal dari database.
- Belum terdapat dedicated modal untuk menambahkan PIC.

### Customer Company

Flow form saat ini kurang tepat.

Jika customer merupakan company:

```text
Customer
├── Company
└── PIC
```

maka company seharusnya dipilih dari daftar company yang sudah tersedia.

Dengan demikian user cukup mengisi:

```text
Company
PIC
```

Jika company belum tersedia, user dapat membuka modal tambah company.

Modal tambah company tersebut sebaiknya menggunakan form yang memang dirancang khusus untuk entity Company, bukan form customer yang dipaksakan untuk membuat company.

Begitu pula jika PIC belum tersedia, tersedia flow untuk membuat Contact/PIC melalui dedicated modal.

### Assigned Sales vs PIC

Peran keduanya harus dibedakan secara eksplisit:

```text
Assigned Sales
= internal employee yang bertanggung jawab melakukan follow-up

PIC / Contact Person
= perwakilan dari pihak customer/company
```

Jangan menggunakan kedua field tersebut untuk merepresentasikan orang dengan responsibility yang sama.

### Import

- Fitur import belum tersedia/belum bekerja.
- Perlu disediakan method backend, validasi file, validasi data, error handling, dan feedback hasil import.

### Attachment

Jika customer dapat memiliki attachment, struktur penyimpanan file perlu konsisten menggunakan metadata attachment dan storage yang sesuai.

---

# 4. MARKETING / SALES MANAGEMENT

> URL saat ini `/marketing`, tetapi halaman sebenarnya merupakan Sales Management. Naming perlu disesuaikan agar URL, menu, controller, dan domain terminology konsisten.

- `name` sebaiknya direpresentasikan dengan struktur data yang konsisten ketika search/filter menghasilkan lebih dari satu data.
- Filter menyebabkan pagination tidak bekerja.
- Hasil search/filter mengubah nilai kolom `roles` menjadi `active/inactive`, sehingga mapping data salah.
- Sort `ASC/DESC` belum dapat ditukar.
- Tombol hapus pada hasil search tidak merespons.
- Tombol show detail menghilang setelah search.
- Email sebaiknya menjadi kolom tersendiri, bukan ditempatkan di bawah nama.
- Email dapat dibuat clickable untuk langsung membuka aplikasi email, misalnya Gmail/mail client.
- Fitur Sales Management secara konsep mirip dengan User Management sehingga sebaiknya tidak menduplikasi logic CRUD, search, sorting, pagination, dan filtering.
- Sales Management dapat menggunakan user yang memiliki role/permission sales lalu menambahkan informasi khusus sales.
- Show Detail harus menampilkan informasi perusahaan/customer yang sudah dihubungi serta status hubungan/follow-up.

---

# 5. TRANSAKSI / DEAL MANAGEMENT

## 5.1 Istilah

Istilah `Transaksi` terlalu umum.

Perlu ditentukan istilah berdasarkan aturan bisnis:

```text
Lead
↓
Opportunity
↓
Deal
↓
Contract
↓
Project
```

`Deal`, `Contract`, dan `Project` bukan sinonim.

Jika aplikasi hanya membutuhkan satu entity untuk merepresentasikan kesepakatan sales, `Deal` dapat digunakan.

Jika contract dan project memiliki lifecycle berbeda, keduanya sebaiknya menjadi entity tersendiri.

---

## 5.2 Table

- Belum menggunakan live search AJAX.
- Data masih ditampilkan menggunakan loop Blade secara statis.
- Belum terdapat filter.
- Sorting `ASC/DESC` belum tersedia.
- Belum dapat menentukan custom `ORDER BY`.
- Search/filter/sort/pagination harus menggunakan pola query yang konsisten dan tidak saling merusak.
- Attachment bukti pada detail belum ditampilkan.
- Attachment harus memiliki metadata dan dapat ditampilkan/diakses dari halaman detail sesuai authorization.

---

## 5.3 Form

### Flow Deal/Lost

Flow saat ini salah karena status `Deals/Lost` ditentukan ketika membuat deal.

Seharusnya lifecycle deal mengikuti pipeline dan stage.

Contoh:

```text
Lead
 ↓
Qualification
 ↓
Proposal
 ↓
Negotiation
 ↓
Won / Lost
```

`Won` dan `Lost` merupakan state/stage akhir berdasarkan business rule, bukan sekadar pilihan bebas ketika membuat record.

### Field

- Terdapat redundansi antara dropdown dan readonly field.
- Field yang merupakan representasi data dari field lain tidak perlu disimpan/ditampilkan dua kali jika tidak memberikan value.
- Pastikan hanya data yang memang perlu disimpan yang menjadi field database.

### KPI

- KPI secara UI kurang tepat apabila ditempatkan di halaman transaksi/deal.
- KPI sebaiknya berada pada dashboard atau halaman analitik jika memang bersifat agregasi/overview.

### Istilah tanggal

`Tanggal Kerja` kurang tepat.

Gunakan terminology berdasarkan business meaning, misalnya:

```text
deal_created_at
contract_signed_at
contract_start_date
estimated_end_date
actual_end_date
```

Namun jangan menganggap tanggal form dikirim otomatis sebagai tanggal kontrak dimulai hanya karena SPK dan DP telah dilampirkan. Hal tersebut harus mengikuti business rule perusahaan.

---

# 6. PIPELINE

Ini merupakan salah satu bagian yang bersifat **fundamental/business logic**, bukan sekadar masalah UI.

- Stage masih hardcoded.
- Stage harus berasal dari database.
- Deal belum dapat berpindah stage.
- Perpindahan deal harus dapat dilakukan secara dinamis.
- Hanya menampilkan data setelah card diklik; perlu UX yang lebih baik untuk melihat data deal.
- `x-overflow` masih aktif/tidak sesuai.
- Lebar scrollbar terlalu besar.
- Hover peek kurang tepat secara UX.

### Pipeline–Stage Relationship

Struktur harus memastikan:

```text
Pipeline
    ├── Stage
    ├── Stage
    └── Stage

Deal
    ├── pipeline_id
    └── current_stage_id
```

Stage yang dipilih oleh sebuah Deal harus berasal dari Pipeline yang sama.

### Stage Transition

Perpindahan dapat dilakukan melalui:

```text
Drag & Drop
```

atau:

```text
Previous Stage
Next Stage
```

Tetapi perpindahan tersebut harus mengikuti business rule.

Contohnya harus ditentukan:

```text
Qualification → Proposal       ✓
Proposal → Negotiation         ✓
Negotiation → Won              ✓

Won → Qualification            ?
Lost → Negotiation             ?
Qualification → Won            ?
```

Jangan hanya mengimplementasikan:

```text
UPDATE deals
SET stage_id = ...
```

tanpa validasi transition.

---

# 7. COMPANY

## 7.1 Table

- Search seharusnya berfokus pada field yang memang bersifat text-searchable, terutama:
    - nama perusahaan
    - deskripsi

- Tipe/jenis perusahaan lebih tepat menjadi filter dropdown.
- Tombol hapus pada hasil search tidak bekerja.
- Tombol show detail menghilang setelah search.
- Belum ada sorting `ASC/DESC`.
- Belum dapat menentukan custom `ORDER BY`.
- Search/filter/sort/pagination harus tetap mempertahankan seluruh action pada setiap row.

---

## 7.2 Action

- Tidak dapat menghapus karena belum terdapat `destroy` method.
- Tombol hapus setelah search tidak merespons.
- Tombol show detail setelah search menghilang.
- Modal show detail tidak dapat ditutup.
- Email pada show detail sebaiknya clickable menuju aplikasi pengelola email.
- Action pada hasil AJAX harus menggunakan event handling yang tetap bekerja setelah DOM berubah.

---

# 8. SALES VISIT

## 8.1 Table

- Tombol edit pada hasil search tidak bekerja.
- Tombol show detail menghilang setelah search.
- Filter saat ini kurang tepat.
- Filter yang lebih sesuai:

```text
Sales
Location
Status
```

bukan hanya:

```text
Sales
Province
```

- Search/filter/sort/pagination perlu menggunakan pola yang konsisten.

---

## 8.2 Action

- Ketika modal baru dibuka, field Company langsung terbuka/focus secara tidak tepat.
- Ketika modal baru dibuka, field Sales langsung mendapat highlight/focus secara tidak tepat.
- Field informasi lokasi tidak disabled secara default.
- Informasi lokasi seharusnya mengikuti Company/location yang dipilih.
- Export belum bekerja karena belum terdapat method.
- Import belum bekerja karena click tidak merespons dan method backend/modal belum lengkap.
- Terdapat redundansi JavaScript modal.
- Ketika data tidak ditemukan dan user ingin menambah data, flow diarahkan ke modal baru yang form-nya lebih minim.
- Sebaiknya gunakan dedicated modal/form untuk entity tersebut agar struktur input konsisten dengan halaman utama Company.
- Hindari memiliki dua definisi modal "Add Company" dengan field dan behaviour berbeda tanpa alasan yang jelas.

---

# 9. MENU MANAGEMENT

## 9.1 Create Menu

Menu tidak seharusnya dapat membuat halaman/fitur baru secara arbitrary.

Jika Superadmin membuat:

```text
New Menu
```

hal tersebut tidak otomatis membuat:

```text
Controller
Route
View
Business Logic
Permission
```

Oleh karena itu, create menu sebaiknya dibatasi pada konfigurasi menu yang memang sudah didukung aplikasi.

Alternatif:

- Create menu hanya digunakan untuk membuat parent menu/configuration entry.
- Route/resource/permission yang dapat dipilih harus berasal dari resource yang memang tersedia.

---

## 9.2 Delete Menu

Menu tidak seharusnya bebas dihapus karena dapat menyebabkan:

```text
Feature masih ada
+
Permission/Navigation entry hilang
=
Feature tidak dapat digunakan dari UI
```

Jika menu memiliki hubungan dengan permission, role, atau resource, penghapusan juga dapat berdampak pada akses aplikasi.

Lebih aman:

```text
Deactivate
Hide
Disable
```

daripada hard delete.

---

## 9.3 Icon

- Kode icon masih berupa text input.
- Icon sebaiknya berasal dari daftar icon yang telah disediakan aplikasi.
- Database menyimpan identifier/code icon.
- UI menyediakan dropdown/icon picker.

Contoh:

```text
icon:
    dashboard
    users
    settings
    building
```

bukan user harus mencari kode icon secara manual di website library.

---

## 9.4 Order

- Edit order menggunakan input manual berpotensi menghasilkan duplicate order.
- Gunakan drag & drop untuk mengubah urutan.
- Sistem harus menjamin urutan menu dalam satu parent tidak memiliki duplicate order.

Contoh:

```text
1 Dashboard
2 Customer
3 Company
4 Sales
```

tidak boleh:

```text
1 Dashboard
2 Customer
2 Company
```

---

## 9.5 Hierarchy

Saat ini hierarchy terlalu terbatas.

Valid:

```text
Settings
└── Role
```

Tetapi kebutuhan sebenarnya dapat lebih kompleks.

Contoh:

```text
Settings
└── User
    ├── Role
    ├── Manage Sales
    └── Manage User
```

Jika sebuah menu mempunyai child, sistem harus dapat menentukan bahwa menu tersebut merupakan parent dan tidak langsung diperlakukan sebagai endpoint/page.

Perlu ada aturan hierarchy yang jelas:

```text
Parent
└── Child
    └── Grandchild
```

Jika grandparent memang tidak diperlukan, maka batasi depth secara jelas. Jangan membiarkan hierarki menjadi ambigu.

---

# 10. FRONTEND & CODEBASE REFACTORING

## 10.1 Separation of Concern

- Pisahkan source `public/js` dan `public/css` yang seharusnya dikelola Vite ke:
    - `resources/js`
    - `resources/css`

- Asset statis yang memang perlu langsung diakses browser tetap dapat berada di `public/`.
- Pecah JavaScript yang panjang menjadi beberapa module berdasarkan responsibility.
- Jangan menumpuk seluruh logic dalam satu file JS.
- Pisahkan JS menjadi external module.
- Hindari JavaScript inline yang panjang di Blade.
- Buat module khusus untuk chart.
- Buat module khusus untuk table/data interaction.
- Buat module khusus untuk modal.
- Buat module khusus untuk cascade location.
- Gunakan reusable component untuk modal.
- Gunakan reusable component untuk chart.
- Gunakan reusable component untuk table jika pola UI memang sama.

---

## 10.2 Laravel Rendering Strategy

Gunakan pendekatan hybrid:

```text
Initial Page
    ↓
Blade / Server-side rendering

Interactive Data
    ↓
AJAX / Fetch
```

Contoh:

```text
Initial table       → Blade
Search              → AJAX
Filter              → AJAX
Pagination           → AJAX
Sort                 → AJAX
Modal detail         → AJAX
Cascade dropdown     → AJAX
Chart                → AJAX
```

Dengan demikian Laravel tetap dimanfaatkan sebagai server-side framework, sementara AJAX digunakan ketika memang memberikan manfaat.

---

# 11. REUSABLE COMPONENTS

Komponen yang memiliki pola berulang sebaiknya dibuat reusable.

Minimal kandidat:

```text
DataTable
Chart
Modal
ConfirmModal
CascadeSelect
Pagination
SearchInput
Filter
```

### DataTable

Idealnya menerima:

```text
endpoint
columns
actions
pagination
sorting
filter
```

dan menangani:

```text
loading
success
empty
error
```

### Confirm Modal

Semua destructive action seperti:

```text
delete
remove
cancel
reject
```

tidak lagi menggunakan:

```javascript
alert();
confirm();
```

Gunakan custom confirmation modal.

---

# 12. REGION / LOCATION

Buat controller/service/endpoint terpusat untuk kebutuhan wilayah.

Daripada:

```text
CompanyController
    getRegencies()
    getDistricts()
    getVillages()

CustomerController
    getRegencies()
    getDistricts()
    getVillages()

SalesVisitController
    getRegencies()
    getDistricts()
    getVillages()
```

gunakan concern terpusat:

```text
Region
├── Province
├── Regency/City
├── District
└── Village
```

Contoh endpoint:

```text
/provinces
/provinces/{province}/regencies
/regencies/{regency}/districts
/districts/{district}/villages
```

Kemudian cascade dropdown menjadi reusable di berbagai halaman.

---

# 13. BACKEND ARCHITECTURE

## 13.1 Controller

Controller sebaiknya tipis dan fokus pada HTTP concern:

```text
Request
↓
Validation
↓
Business Logic
↓
Response
```

Jangan menumpuk business logic di controller.

---

## 13.2 Form Request

Gunakan Form Request untuk validasi yang kompleks atau reusable:

```text
StoreCustomerRequest
UpdateCustomerRequest
StoreCompanyRequest
UpdateCompanyRequest
StoreDealRequest
UpdateDealRequest
```

---

## 13.3 Business Logic

Logic yang bersifat domain/business rule dipisahkan dari controller.

Terutama:

```text
Deal creation
Stage transition
Deal Won/Lost
Import
Attachment
Company/Contact relationship
```

---

## 13.4 Query

Pola query untuk table harus konsisten:

```text
Request
↓
Search
↓
Filter
↓
Sort
↓
Pagination
↓
Response
```

Jangan membuat logic berbeda-beda untuk kombinasi:

```text
search
search + filter
search + sort
filter + sort
search + filter + sort
```

Gunakan query builder/Eloquent secara composable.

---

# 14. AJAX & API RESPONSE

Karena banyak fitur menggunakan AJAX, response backend harus konsisten.

Success:

```json
{
    "success": true,
    "message": "Customer berhasil dibuat",
    "data": {}
}
```

Error:

```json
{
    "success": false,
    "message": "Customer gagal dibuat",
    "errors": {}
}
```

Dengan format konsisten, frontend tidak perlu memiliki handling berbeda untuk setiap endpoint.

---

# 15. ERROR, EMPTY & LOADING STATE

Semua halaman/component yang mengambil data asynchronous perlu memiliki state yang jelas:

```text
Initial
   ↓
Loading
   ↓
Success
   ├── Data
   └── Empty

Error
   ↓
Retry
```

Contoh:

```text
Loading...
```

```text
Belum ada data.
```

```text
Gagal mengambil data.
[ Coba Lagi ]
```

Jangan hanya menangani kondisi ketika data berhasil.

---

# 16. ATTACHMENT & FILE STORAGE

Karena sistem memiliki:

```text
SPK
Bukti DP
Attachment
Import
Export
```

perlu ada architecture file yang konsisten.

Database menyimpan metadata:

```text
attachments
├── id
├── attachable_type
├── attachable_id
├── filename
├── path
├── mime_type
├── size
├── uploaded_by
└── created_at
```

File fisiknya disimpan pada storage.

Perlu ditentukan juga:

- siapa yang dapat melihat file
- siapa yang dapat menghapus
- siapa yang dapat mengupload
- batas ukuran
- tipe file yang diperbolehkan
- penamaan file
- lokasi storage

---

# 17. DATABASE & DOMAIN MODEL

Database perlu diperiksa kembali berdasarkan domain, bukan hanya berdasarkan kebutuhan halaman.

Minimal pastikan relationship berikut jelas:

```text
Company
    ↓
Contact / PIC

User
    ↓
Sales

Customer
    ↓
Company
    ↓
Contact / PIC
    ↓
Assigned Sales

Deal
    ↓
Customer
    ↓
Pipeline
    ↓
Current Stage
```

Perlu diperiksa:

- foreign key
- relationship
- nullable field
- unique constraint
- referential integrity
- cascade behavior
- naming convention

Database tidak boleh hanya mengikuti struktur form.

Database harus mengikuti **domain model**.

---

# 18. DEAL STAGE HISTORY

Selain menyimpan:

```text
deal.current_stage_id
```

sistem membutuhkan history perpindahan stage.

Contoh:

```text
deal_stage_histories

id
deal_id
from_stage_id
to_stage_id
changed_by
changed_at
reason
```

Contoh history:

```text
10:00
Qualification → Proposal
oleh Sales A

11:30
Proposal → Negotiation
oleh Sales A

14:00
Negotiation → Won
oleh Sales B
```

Hal ini diperlukan untuk:

- timeline
- audit
- mengetahui durasi setiap stage
- analisis conversion
- mengetahui siapa yang memindahkan deal
- debugging business process

---

# 19. TIMELINE & AUDIT LOG

Sistem membutuhkan dua konsep yang dapat dibedakan.

## Business Timeline

Berisi aktivitas yang relevan bagi perjalanan customer/deal:

```text
Deal dibuat
Customer dihubungi
Stage berubah
Meeting dilakukan
Attachment diupload
Deal Won
```

## Audit Log

Berisi perubahan sistem:

```text
who
what
when
before
after
```

Contoh:

```text
User A
UPDATE
Deal #123
stage:
Proposal → Negotiation
2026-09-04 14:00
```

Audit log sebaiknya immutable.

---

# 20. DATABASE TRANSACTION

Operasi yang mengubah beberapa resource sekaligus perlu menggunakan database transaction jika seluruh operasi harus berhasil sebagai satu kesatuan.

Contoh membuat Deal:

```text
Create Deal
    +
Create Attachment
    +
Create Stage History
    +
Create Activity
```

Jika salah satu proses wajib gagal, perubahan sebelumnya harus di-rollback.

Tujuannya menghindari kondisi data parsial/inconsistent.

---

# 21. SOFT DELETE & DATA LIFECYCLE

Untuk entity tertentu yang memiliki historical relationship, pertimbangkan soft delete:

```text
deleted_at
```

terutama untuk data seperti:

```text
Company
Customer
User
Deal
```

jika business requirement mengharuskan data tetap dapat direferensikan.

Namun tidak semua tabel harus menggunakan soft delete.

Data audit/history yang bersifat immutable tidak seharusnya ikut dihapus hanya karena entity utama dihapus.

---

# 22. SECURITY

Pastikan seluruh action backend memiliki authorization yang benar.

Menu yang disembunyikan **bukan mekanisme security**.

User tetap tidak boleh dapat melakukan:

```text
GET /admin/users
POST /customers
DELETE /companies/10
```

hanya dengan mengetahui URL.

Authorization harus ditegakkan pada backend melalui mekanisme seperti:

```text
Middleware
Policy
Gate
Permission
```

Selain authorization, periksa:

```text
CSRF
XSS
Mass Assignment
SQL Injection
File Upload Security
Rate Limiting
Session Security
```

---

# 23. PRODUCTION & CODE QUALITY

- Hapus file yang tidak digunakan.
- Hapus JS/CSS yang obsolete.
- Hapus `console.log()` yang tidak diperlukan.
- Konsistenkan nama icon.
- Konsistenkan nama field.
- Konsistenkan terminology.
- Konsistenkan naming controller/model/component.
- Hindari dua component yang melakukan fungsi sama tetapi memiliki behaviour berbeda tanpa alasan.
- Ubah inline style menjadi utility class Tailwind jika memang sesuai.
- Gunakan Tailwind CLI/build process yang sesuai untuk production.
- Periksa responsive layout.
- Periksa accessibility.
- Periksa loading/error/empty state.
- Periksa performa query dan request AJAX.

---

# 24. CALENDAR

- Halaman kalender saat ini menggunakan React.
- Jika aplikasi utama menggunakan Laravel Blade dan React tidak memberikan kebutuhan khusus, kalender dapat dikonversi menjadi Blade/JavaScript agar architecture lebih konsisten.
- Hindari penggunaan framework frontend berbeda hanya untuk satu halaman apabila tidak terdapat alasan teknis yang kuat.

---

# 25. MIGRATION & SEEDER

Database tidak lagi bergantung pada restore manual melalui:

```text
terminal
+
.sql
```

Gunakan:

```text
Migration
Seeder
```

sebagai source of truth untuk struktur dan initial/demo data.

Migration digunakan untuk:

```text
create table
alter table
add column
add foreign key
add index
```

Seeder digunakan untuk:

```text
role
permission
menu
user
province
regency
district
village
pipeline
stage
dummy data
```

Dengan demikian database dapat direcreate secara konsisten melalui Laravel.

---

# 26. NEW FEATURES

Fitur baru yang perlu ditambahkan:

### Pipeline

- Dynamic Pipeline
- Dynamic Stage
- Dynamic Deal movement
- Drag & Drop
- Next/Previous Stage
- Stage Transition Rules
- Stage History

### UI

- Reusable confirmation modal
- Detail modal sesuai dengan field/entity sebenarnya
- Loading state
- Empty state
- Error state
- Retry state

### Data

- Migration seluruh tabel
- Seeder seluruh data yang diperlukan
- Attachment management
- Timeline
- Audit history

---

# 27. FATAL / PRIORITAS TERTINGGI

Bagian ini harus dikerjakan sebelum polishing UI.

## 27.1 Database

- Struktur database masih perlu diperbaiki.
- Pastikan seluruh entity benar-benar merepresentasikan domain.
- Pastikan relationship antar entity benar.
- Pastikan lifecycle data benar.
- Pastikan foreign key dan referential integrity benar.

## 27.2 Pipeline & Deal

- Perbaiki business logic Pipeline.
- Perbaiki relationship Pipeline → Stage → Deal.
- Hilangkan hardcoded stage.
- Implementasikan dynamic stage.
- Implementasikan transition.
- Implementasikan stage history.
- Tentukan aturan Won/Lost.
- Tentukan lifecycle Deal secara jelas.

## 27.3 Audit & Timeline

- Implementasikan stage history.
- Implementasikan business timeline.
- Implementasikan audit log.
- Pastikan historical record tidak mudah hilang atau berubah.

## 27.4 Authorization

- Pastikan permission benar-benar ditegakkan pada backend.
- Menu hanya menjadi representasi UI dari permission/resource.
- Jangan menjadikan menu sebagai satu-satunya mekanisme pembatasan akses.

---

# 28. URUTAN IMPLEMENTASI YANG DIREKOMENDASIKAN

Perbaikan sebaiknya tidak dilakukan berdasarkan urutan halaman. Prioritaskan berdasarkan dependency.

```text
PHASE 1
DOMAIN & DATABASE
│
├── Entity
├── Relationship
├── Pipeline
├── Stage
├── Deal
├── Company
├── Customer
├── Contact/PIC
├── Sales
├── Attachment
├── Timeline
└── Audit
        ↓
PHASE 2
DATABASE MIGRATION & SEEDER
        ↓
PHASE 3
BACKEND FOUNDATION
│
├── Model relationship
├── Form Request
├── Business Logic
├── Region Controller
├── Search
├── Filter
├── Sort
├── Pagination
├── Import
├── Export
└── AJAX Response
        ↓
PHASE 4
PIPELINE & DEAL
│
├── Dynamic Pipeline
├── Dynamic Stage
├── Stage Transition
├── Drag & Drop
├── Stage History
└── Timeline
        ↓
PHASE 5
FRONTEND ARCHITECTURE
│
├── Vite
├── JS Modules
├── Reusable Modal
├── Confirm Modal
├── DataTable
├── Cascade Select
├── Chart
└── AJAX State
        ↓
PHASE 6
PAGE IMPLEMENTATION
│
├── Customer
├── Company
├── Sales Management
├── Sales Visit
├── Deal
├── Pipeline
├── Dashboard
├── Navbar
└── Sidebar
        ↓
PHASE 7
POLISHING & PRODUCTION
│
├── UX
├── Responsive
├── Accessibility
├── Loading/Empty/Error State
├── Tailwind Build
├── Code Cleanup
├── Security Review
└── Performance Review
```

---

# 29. RINGKASAN PRIORITAS

### P0 — Fundamental / Fatal

```text
1. Database/domain model
2. Pipeline & Stage business logic
3. Deal lifecycle
4. Company / Customer / Contact / Sales relationship
5. Stage history
6. Timeline & audit
7. Attachment architecture
```

### P1 — Backend Architecture

```text
1. Separation of concern
2. Region/Cascade terpusat
3. Form Request
4. Business logic/service
5. Search/filter/sort/pagination architecture
6. AJAX response standardization
7. Database transaction
8. Import/export
9. Data lifecycle / soft delete
```

### P2 — Frontend Architecture

```text
1. Vite/resources
2. JS modularization
3. Reusable DataTable
4. Reusable Modal
5. Reusable Confirm Modal
6. Reusable Chart
7. Reusable Cascade Select
8. AJAX state management
9. Loading/Empty/Error state
```

### P3 — Feature Completion

```text
1. Customer
2. Company
3. Sales Management
4. Sales Visit
5. Deal
6. Pipeline
7. Dashboard
8. Navbar
9. Sidebar
10. Menu Management
```

### P4 — Polish

```text
1. UX
2. Responsive
3. Accessibility
4. Terminology
5. Naming consistency
6. Icon consistency
7. Cleanup
8. Tailwind production build
9. Performance
```

---

## Kesimpulan

Hasil revisi ini menunjukkan bahwa masalah aplikasi sebenarnya dapat dikelompokkan menjadi **tiga lapisan utama**:

```text
                    APPLICATION
                         │
        ┌────────────────┼────────────────┐
        ↓                ↓                ↓
    DOMAIN/DATA      BACKEND          FRONTEND
        │                │                │
   Deal/Pipeline      CRUD/API       Component
   Stage/Company      Validation     AJAX
   Customer/PIC       Query          Modal
   Timeline           Import         Table
   Audit              Attachment     Chart
   Relationship       Transaction    UX
```

**Database dan business logic adalah dependency paling atas.** Jika bagian tersebut belum benar, memperbaiki chart, modal, sidebar, atau styling hanya menghasilkan UI yang lebih rapi di atas fondasi yang masih salah.

Dari seluruh daftar, dua bagian yang paling perlu diperlakukan sebagai **refactoring domain**, bukan sekadar bug fixing, adalah **model Company–Customer–Contact–Sales** dan **Pipeline–Stage–Deal lifecycle**. Setelah keduanya benar, sebagian besar masalah form, modal, detail, table, dan dashboard akan jauh lebih mudah diselesaikan.
