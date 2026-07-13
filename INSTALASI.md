# PANDUAN INSTALASI — COATING CEPAT di InfinityFree

## Kredensial Database

| Setting  | Nilai                        |
|----------|------------------------------|
| DB_HOST  | sql204.infinityfree.com      |
| DB_NAME  | if0_42105007_coatingcepat    |
| DB_USER  | if0_42105007                 |
| DB_PASS  | Wuq1r9wkIjdfL                |
| PORT     | 3306                         |

> File `config/config.php` **sudah diisi** dengan nilai di atas. Tidak perlu ubah lagi.

---

## ⚠️ WAJIB DILAKUKAN SEBELUM UPLOAD — Import Database

> Kalau langkah ini dilewati, website akan tampil **"Gagal Terhubung ke Database"**!

1. Login ke panel InfinityFree → klik **phpMyAdmin** di samping database `if0_42105007_coatingcepat`
2. Klik tab **Import**
3. Import `schema.sql` terlebih dahulu → klik **Go** → tunggu sampai sukses
4. Setelah selesai, import `seed.sql` → klik **Go** → tunggu sampai sukses

---

## Langkah 2 — Upload File ke Hosting

1. Buka **File Manager** di panel InfinityFree (atau pakai FTP)
2. Masuk ke folder `htdocs/`
3. Upload **semua isi zip ini** (bukan foldernya, tapi **isinya langsung**)
   - Pastikan `index.php` ada langsung di dalam `htdocs/`
4. Pastikan folder `uploads/` punya permission **755**

---

## Langkah 3 — Akses Website

- **Website:** `https://nama-domain-kamu.infinityfreeapp.com`
- **Admin Panel:** `https://nama-domain-kamu.infinityfreeapp.com/admin/`
- **Login Admin Default:**
  - Email: `admin@coatingcepat.com`
  - Password: `Admin123!`
  - ⚠️ Segera ganti password setelah login pertama!

---

## Catatan Penting

- Upload gambar maksimal **2 MB** per file
- Folder `uploads/` harus bisa ditulis (permission 755)
- Jika muncul error **500**, tambahkan sementara di baris pertama `index.php`:
  ```php
  ini_set('display_errors', 1); error_reporting(E_ALL);
  ```
  Hapus setelah selesai debug.
