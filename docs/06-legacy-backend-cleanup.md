# DOKUMENTASI PEMBERSIHAN FILE LEGACY BACKEND & REFRACTORING CLEAN ARCHITECTURE

Dokumen ini mencatat tindakan pembersihan (_cleanup_) file-file backend *legacy* yang tidak sesuai dengan prinsip **Clean Architecture** dan **Separation of Concerns (SoC)** pada proyek CRM.

---

## 1. DAFTAR FILE LEGACY YANG DIHAPUS

Sebanyak 16 controller *monolithic legacy* yang berada secara langsung di akar direktori `app/Http/Controllers/` telah dihapus:

| No | Nama File Legacy | Alasan Penghapusan & Masalah Clean Architecture |
|---|---|---|
| 1 | `CompanyController.php` | Monolitik, menggabungkan view HTML, AJAX dropdown, query raw Eloquent, dan logika transaksi dalam 1 file. Digantikan oleh `Web\OrganizationController`, `Api\V1\OrganizationController`, `OrganizationService`, dan `Api\V1\RegionController`. |
| 2 | `CustomerController.php` | Menggabungkan logika ekspor/impor CSV, AJAX search, dan CRUD pelanggan tanpa pemisahan Service/Request. Digantikan oleh `Web\ClientController`, `Api\V1\ClientController`, dan `ClientService`. |
| 3 | `PicController.php` | Menggabungkan manajemen kontak/PIC perusahaan dengan AJAX modal inline. Digantikan oleh `Web\OrganizationContactController`, `Api\V1\OrganizationContactController`, dan `OrganizationContactService`. |
| 4 | `PipelineController.php` | Controller monolitik untuk tampilan Kanban dan kalkulasi stage. Digantikan oleh `Web\PipelineController`, `Api\V1\PipelineController`, dan `PipelineService`. |
| 5 | `TransaksiController.php` | Naming convention bahasa Indonesia, pencampuran kueri transaksi internal dengan otorisasi manual. Digantikan oleh `Web\DealController`, `Api\V1\DealController`, dan `DealService`. |
| 6 | `SalesVisitController.php` | Menggabungkan query geografis wilayah, relasi sales visit, dan pencatatan riwayat secara inline. Digantikan oleh `Web\SalesVisitController`, `Api\V1\SalesVisitController`, dan `SalesVisitService`. |
| 7 | `UserController.php` | Controller monolitik manajemen pengguna tanpa Form Request dan Resource DTO. Digantikan oleh `Web\UserController`, `Api\V1\UserController`, dan `UserService`. |
| 8 | `RoleController.php` | Menggabungkan penetapan menu-role secara manual dan query Role model inline. Digantikan oleh `Web\RoleController`, `Api\V1\RoleController`, dan `RoleService`. |
| 9 | `MenuController.php` | Pencampuran query icon menu dan tree structure menu di controller. Digantikan oleh `Web\MenuController`, `Api\V1\MenuController`, dan `MenuService`. |
| 10 | `RegionController.php` | Legacy region controller yang belum menggunakan API Resource baku dan Caching service. Digantikan oleh `Api\V1\RegionController` dan `RegionService`. |
| 11 | `SalesController.php` | Controller legacy manajemen tim sales. Fungsi terintegrasi ke dalam `Web\UserController` / `Api\V1\UserController` berfilter role sales. |
| 12 | `SalesVisitTrendController.php` | Pelanggaran SoC: Mengandung SQL raw query untuk kalkulasi tren grafik sales visit secara langsung di controller. |
| 13 | `SalesPerformanceController.php` | Pelanggaran SoC: Mengandung kalkulasi performa sales dan perbandingan kuota secara langsung di controller. |
| 14 | `CompanyChartController.php` | Pelanggaran SoC: Controller berisi logika agregasi statistik geografis perusahaan secara hardcoded. |
| 15 | `TrendPageController.php` | Controller tampilan tanpa abstraksi service atau integrasi API V1. |
| 16 | `CalendarController.php` | Controller event kalender dengan query inline tanpa otorisasi Policy dan FormRequest. |

---

## 2. IMPACT / DAMPAK PENGHAPUSAN

### A. Dampak Negatif / Isu Sementara (Risiko)
1. **Broken Legacy Routes**: Seluruh rute pada `routes/web.php` yang mereferensikan namespace `App\Http\Controllers\*` (controller lama) tidak lagi dapat dieksekusi sebelum rute diperbarui.
2. **Broken Legacy AJAX Inlines**: Script JavaScript bawaan Blade yang memanggil endpoint lama (misal `/company/get-regencies/{id}` atau `/transaksi/search`) memerlukan penyesuaian/proxy ke rute Clean Architecture baru.

### B. Dampak Positif (Manfaat Arsitektural)
1. **Penghilangan Ambiguitas Namespace**: Tidak ada lagi duplikasi nama kelas controller seperti `App\Http\Controllers\UserController` vs `App\Http\Controllers\Web\UserController` vs `App\Http\Controllers\Api\V1\UserController`.
2. **Enforcement Separation of Concerns (SoC)**: Memaksa pengembang untuk memisahkan rute Web Blade View (`App\Http\Controllers\Web\*`) dan REST API V1 (`App\Http\Controllers\Api\V1\*`).
3. **Standarisasi Penamaan Entitas**: Menghilangkan nama controller bahasa Indonesia (`TransaksiController`, `PicController`) dan menyelaraskan seluruh entitas ke bahasa Inggris RESTful standard (`DealController`, `OrganizationContactController`).

---

## 3. SOLUSI IMPLEMENTASI (SOLUTION)

Untuk mengatasi dampak dari pembersihan file legacy di atas, diterapkan arsitektur baru berbasis 4 lapisan dasar:

```
[ HTTP Request ]
       │
       ▼
[ Web/API Controller ] ── (Validasi) ──► [ Form Request ]
       │
       ▼
[ Domain Service Layer ] ── (Otorisasi) ──► [ Policy ]
       │
       ▼
[ Eloquent Models ]
       │
       ▼
[ Resource DTO / Blade View ]
```

### A. Web Blade Controllers (`App\Http\Controllers\Web\*`)
- Hanya bertugas mengembalikan tampilan Blade (`return view('pages.company', ...)`) atau memberikan data awal yang diperlukan oleh halaman Web.
- Tidak menangani query SQL, kalkulasi logika bisnis, atau pengolahan payload AJAX.

### B. REST API V1 Controllers (`App\Http\Controllers\Api\V1\*`)
- Berada di bawah namespace `App\Http\Controllers\Api\V1` dan menerima permintaan berformat JSON.
- Membalas menggunakan **Laravel JsonResource** (`App\Http\Resources\*`) dengan struktur data baku JSON REST API.

### C. Abstraksi Business Logic (`App\Services\*`)
- Seluruh logika transaksi, kalkulasi tren, pengelolaan file, dan operasi CRUD dipindahkan ke kelas Domain Service.
- Contoh: `OrganizationService`, `ClientService`, `DealService`, `SalesVisitService`, `RegionService`.

### D. Pemisahan Rute (`routes/web.php` & `routes/api.php`)
- `routes/web.php`: Mengurus autentikasi sesi web (login/logout/forgot-password), rute halaman Web Blade, serta proxy endpoint AJAX legacy agar script Blade yang ada tetap berjalan seamlessly.
- `routes/api.php`: Berisi grup rute REST API V1 (`/api/v1/...`) berproteksi middleware `auth:sanctum`.

---

## 4. PRAKTIK TERBAIK (BEST PRACTICES)

1. **Prinsip Single Responsibility (SRP)**:
   - Satu Controller method hanya bertugas menterjemahkan HTTP request ke Service, dan memformat hasilnya menjadi HTTP response (View atau JsonResource).
2. **Strict Namespacing**:
   - Selalu tempatkan rute tampilan di `App\Http\Controllers\Web\` dan rute API di `App\Http\Controllers\Api\V1\`.
   - Dilarang membuat controller baru secara langsung di `App\Http\Controllers\`.
3. **Validasi Terpusat via Form Request**:
   - Jangan menulis `$request->validate(...)` di controller. Gunakan kelas `FormRequest` khusus untuk memvalidasi input data.
4. **Otorisasi Terpusat via Policy**:
   - Manfaatkan `$this->authorize('viewAny', Model::class)` atau Spatie Permission middleware, bukan pengecekan role/permission manual di controller.
5. **RESTful Naming Conventions**:
   - Gunakan nama plural ber-bahasa Inggris untuk entitas controller dan URI rute (`organizations`, `clients`, `deals`, `sales-visits`, `organization-contacts`, `regions`).
