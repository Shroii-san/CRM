# ARSITEKTUR FRONTEND & REUSABLE COMPONENTS

Dokumen ini memuat standar pengelolaan aset _frontend_, strategi _rendering_, dan panduan pembuatan antarmuka (UI) menggunakan komponen yang dapat digunakan kembali (_reusable components_).

---

## 1. MANAJEMEN ASET & VITE

Aplikasi menggunakan **Vite** sebagai _module bundler_ utama. Penempatan aset harus mengikuti aturan ketat berikut:

- **`resources/css` & `resources/js`:** Tempat untuk semua _source code_ CSS (Tailwind) dan JavaScript aplikasi. Seluruh _script_ interaktif, logika _chart_, _table_, dan _modal_ harus ditulis di sini agar diproses (di-_build_) dan oleh Vite.
- **`public/`:** HANYA digunakan untuk aset statis yang tidak perlu diproses, seperti gambar, logo perusahaan, favicon, atau dokumen _dummy_.
- **Larangan:** Tidak boleh meletakkan file `.js` atau `.css` aplikasi langsung di folder `public/js` atau `public/css`.

---

## 2. STRATEGI RENDERING HYBRID

Aplikasi menggunakan pendekatan _hybrid_ antara _Server-Side Rendering_ (SSR) dan _Client-Side Rendering_ (CSR):

1. **Initial Page Load:** Render struktur dasar halaman, kerangka tabel, dan UI utama menggunakan **Laravel Blade**.
2. **Interactive Data:** Data yang dinamis (seperti hasil _search_, _filter_, ganti halaman/pagination, dan modal detail) harus diambil menggunakan **AJAX/Fetch**.
3. **Event Binding:** Karena DOM akan sering berubah akibat hasil AJAX, seluruh interaksi _event_ JavaScript (seperti klik tombol hapus atau detail) harus menggunakan teknik _Event Delegation_ agar tombol tetap berfungsi setelah data di-render ulang.

---

## 3. MODULARITAS JAVASCRIPT

Dilarang menuliskan kode JavaScript (_inline script_) yang panjang dan kompleks secara langsung di dalam file `.blade.php`.

JavaScript harus dipecah menjadi modul-modul terpisah berdasarkan fungsionalitasnya:

- Modul khusus untuk **Chart**
- Modul khusus untuk **DataTable** (Pencarian & Pagination AJAX)
- Modul khusus untuk **Modal**
- Modul khusus untuk **Cascade Location**

---

## 4. REUSABLE COMPONENTS

Komponen UI yang pola penggunaannya berulang wajib dibuat menjadi komponen _reusable_ (bisa menggunakan _Blade Components_ `x-component` atau modul JS).

### A. DataTable Component

Komponen tabel harus menerima parameter: `endpoint`, `columns`, `actions`, `pagination`, `sorting`, dan `filter`. Tabel ini menangani pengambilan data AJAX dan menampilkan state yang sesuai (lihat Poin 5).

### B. Confirm Modal Component

Semua tindakan destruktif (_delete_, _remove_, _cancel_, _reject_) **dilarang** menggunakan bawaan browser:

```javascript
// DILARANG:
alert("Berhasil!");
confirm("Yakin ingin menghapus?");
```

Gunakan UI custom Confirmation Modal dari aplikasi agar desain konsisten dan tidak diblokir oleh browser.

### C. Cascade Select Component

Komponen dropdown berantai yang saling bergantung (seperti: Provinsi → Kabupaten → Kecamatan). Komponen ini memanggil API wilayah secara otomatis berdasarkan pilihan sebelumnya.

---

## 5. STANDAR AJAX STATE MANAGEMENT

Semua halaman atau komponen yang mengambil data secara asinkron (AJAX) wajib menangani 4 state (kondisi) berikut secara visual di UI:

- **Loading State:** Menampilkan indikator (seperti spinner atau skeleton loading) saat data sedang diambil.
- **Success State:** Menampilkan data (table/chart/list) dengan benar setelah respons berhasil.
- **Empty State:** Menampilkan ilustrasi atau teks yang jelas (misal: "Belum ada data pelanggan") jika respons berhasil namun data kosong.
- **Error & Retry State:** Menampilkan pesan kesalahan (misal: "Gagal mengambil data") beserta tombol `[ Coba Lagi ]` jika koneksi gagal atau terjadi error di backend.

Dilarang membiarkan UI dalam keadaan blank (kosong tanpa indikator) apabila terjadi kegagalan pengambilan data.

---

## 6. STRUKTUR NAVBAR & SIDEBAR

- **Separation:** Logika dan file view untuk Navbar dan Sidebar harus dipisah (independent).
- **Sidebar Scrolling:** Layout sidebar harus memiliki penanganan scrolling yang baik agar area menu dapat di-scroll secara independen tanpa menutupi atau bertabrakan dengan tombol Logout di bagian bawah.
- **Notification:** Data notifikasi pada Navbar tidak boleh hardcoded. Harus diambil dari database dan memiliki status minimal: `unread` dan `read`.
