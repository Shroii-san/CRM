# ARSITEKTUR BACKEND & STANDAR API

Dokumen ini mendefinisikan standar penulisan kode backend (Laravel) dan format interaksi data (AJAX/API) untuk aplikasi ini. Seluruh pengembangan fitur baru dan _refactoring_ kode lama wajib mengikuti standar yang ada di dokumen ini.

---

## 1. SEPARATION OF CONCERNS (Pemisahan Logika)

Controller di dalam aplikasi harus dipertahankan setipis mungkin (_thin controllers_). Controller hanya bertanggung jawab pada lapisan HTTP dan tidak boleh berisi logika bisnis yang kompleks.

**Alur Data yang Diwajibkan:**

```text
Request HTTP
   ↓
Validation (Form Request)
   ↓
Business Logic (Service Layer / Action)
   ↓
Response (JSON / View)
```

**Aturan Penulisan:**

- Jangan menumpuk logika penentuan `stage`, `won/lost`, atau pembuatan relasi multi-tabel di dalam Controller.
- Pindahkan logika berat ke dalam Service Class atau Action Class.

---

## 2. FORM REQUEST VALIDATION

Untuk validasi form yang melibatkan proses Create atau Update, dilarang menggunakan `$request->validate()` secara langsung di dalam controller apabila validasinya kompleks atau dapat digunakan kembali (reusable).

Gunakan Form Request bawaan Laravel:

- `StoreCustomerRequest`
- `UpdateCustomerRequest`
- `StoreDealRequest`
- `UpdateDealRequest`

---

## 3. STANDAR RESPONSE AJAX & API

Karena aplikasi ini menggunakan pendekatan hybrid (blade untuk render awal, AJAX untuk interaksi data), semua endpoint API atau AJAX wajib mengembalikan format JSON yang seragam. Hal ini bertujuan agar frontend tidak perlu membuat penanganan (handling) error yang berbeda-beda untuk setiap halaman.

**Format Berhasil (Success)**

```json
{
    "success": true,
    "message": "Data berhasil disimpan.",
    "data": {
        "id": 1,
        "name": "PT Contoh Data"
    }
}
```

**Format Gagal (Error)**

```json
{
    "success": false,
    "message": "Gagal menyimpan data, periksa kembali inputan Anda.",
    "errors": {
        "email": ["Email sudah terdaftar."],
        "phone": ["Format nomor telepon tidak valid."]
    }
}
```

---

## 4. PATTERN QUERY: TABLE & DATAGRID

Pencarian, pemfilteran, pengurutan, dan paginasi harus dapat digabungkan tanpa saling merusak hasil kueri. Gunakan pendekatan query builder secara composable.

**Urutan eksekusi kueri:**

```text
Search (Pencarian Teks)
   ↓
Filter (Kondisi Dropdown/Status)
   ↓
Sort (Order By ASC/DESC)
   ↓
Pagination (Limit & Offset)
```

**Format Request URL Standard:**

```text
/customers?search=andi&status=active&sort=name&direction=asc&page=2
```

---

## 5. API REGION / LOCATION TERPUSAT

Dilarang menduplikasi fungsi pengambilan data wilayah (Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan) di berbagai controller (misal: `CompanyController`, `CustomerController`, `SalesVisitController`).

Gunakan satu set API terpusat untuk kebutuhan Cascade Dropdown:

- `GET /api/provinces`
- `GET /api/provinces/{province}/regencies`
- `GET /api/regencies/{regency}/districts`
- `GET /api/districts/{district}/villages`

---

## 6. DATABASE TRANSACTIONS

Setiap operasi yang memanipulasi lebih dari satu entitas atau tabel wajib dibungkus dalam `DB::transaction()`. Jika salah satu proses gagal, seluruh perubahan akan di-rollback untuk mencegah anomali data (data parsial/inconsistent).

**Contoh Kasus Wajib Transaksi:**

```text
Create Deal
    + Create Attachment (Bukti/SPK)
    + Create Stage History
    + Create Activity Timeline
```

---

## 7. SECURITY & AUTHORIZATION

Menyembunyikan menu pada sidebar UI bukanlah mekanisme keamanan. Semua aksi backend harus dilindungi oleh aturan otorisasi.

**Aturan Keamanan:**

1. **Middleware & Policy** — Gunakan Policy atau Gate Laravel untuk memastikan user berhak melakukan operasi `GET`, `POST`, `PUT`, `DELETE` pada resource tertentu.
2. **Mass Assignment** — Pastikan properti `$fillable` di Model sudah diatur dengan ketat untuk mencegah perubahan data ilegal.
3. **Immutability Audit Log** — Data pada tabel riwayat (seperti `deal_stage_histories` dan `audit_logs`) bersifat immutable dan tidak boleh diizinkan untuk diubah (update) melalui endpoint apa pun.
