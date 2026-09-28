# CRUD Sistem Inventory - PHP Native (PDO + MySQL)

Proyek latihan uji kompetensi tanpa framework: Kategori (CRUD), Barang (CRUD + detail + cari + filter + paginasi), Transaksi Stok (barang masuk/keluar), dan Dashboard.

Tampilan memakai Bootstrap 5 lewat CDN (butuh internet saat membuka halaman). Tidak perlu Composer atau npm.

## Kebutuhan
- XAMPP / Laragon (PHP 8.0+ dan MySQL/MariaDB)

## Langkah Pemasangan (XAMPP)

### 1. Taruh folder proyek
Ekstrak zip, lalu salin folder `inventory-native` ke:
```
C:\xampp\htdocs\inventory-native
```

### 2. Nyalakan layanan
Buka XAMPP Control Panel, klik **Start** pada **Apache** dan **MySQL**.

### 3. Impor database
1. Buka http://localhost/phpmyadmin
2. Klik tab **Import**
3. Pilih file `database/inventory_db.sql`, lalu klik **Import**

File ini otomatis membuat database `inventory_db`, semua tabel (termasuk `users`), dan data contoh.

> Database lama sudah berisi data? Jangan impor `inventory_db.sql` lagi (file itu menghapus semua tabel). Impor `database/tambah_users.sql` saja untuk menambah tabel `users`.

### 4. Cek konfigurasi (jika perlu)
Buka `config/koneksi.php`:
- `BASE_URL`: harus sama dengan nama folder di htdocs. Jika folder Anda ganti nama menjadi `toko`, ubah menjadi `/toko`.
- `$user` dan `$pass`: default XAMPP adalah `root` dan kosong.

### 5. Buat akun admin pertama
Buka http://localhost/inventory-native/ - karena belum ada user, Anda otomatis diarahkan ke `setup.php`. Isi nama, username, dan password (minimal 8 karakter). Halaman setup terkunci sendiri setelah akun pertama dibuat, jadi lakukan langkah ini segera setelah instalasi.

### 6. Login
Masuk lewat `login.php`. Tombol **Keluar** ada di pojok kanan navbar.

## Struktur Folder

```
inventory-native/
├── index.php                 Dashboard
├── login.php                 Halaman login
├── logout.php                Logout (POST + CSRF)
├── setup.php                 Buat admin pertama (terkunci setelah ada user)
├── config/koneksi.php        Koneksi PDO + session + BASE_URL
├── database/inventory_db.sql Struktur tabel + data contoh
├── includes/
│   ├── functions.php         e(), rupiah(), redirect(), flash, post()
│   ├── auth.php              wajib_login(), timeout sesi, CSRF
│   ├── header.php            Navbar + tampilan pesan flash
│   └── footer.php
├── kategori/                 index, tambah, edit, hapus, _form
├── barang/                   index, tambah, edit, hapus, detail, _form, _validasi
└── transaksi/                index, tambah
```

## Peta Belajar (sering ditanyakan saat uji kompetensi)

| Konsep | Lokasi |
|---|---|
| Koneksi PDO + try/catch | `config/koneksi.php` |
| Struktur tabel, primary key, foreign key (RESTRICT / CASCADE) | `database/inventory_db.sql` |
| CREATE (INSERT dengan prepared statement) | `kategori/tambah.php`, `barang/tambah.php` |
| READ (SELECT, JOIN, GROUP BY, COUNT) | `kategori/index.php`, `barang/index.php` |
| UPDATE | `kategori/edit.php`, `barang/edit.php` |
| DELETE lewat POST (bukan GET) | `kategori/hapus.php`, `barang/hapus.php` |
| Validasi form + nilai lama tetap tampil (sticky form) | `barang/_validasi.php`, `barang/_form.php` |
| Cek unik saat edit (`id != ?`) | `kategori/edit.php` |
| Cegah SQL Injection (prepared statement) | semua file yang memakai `$pdo->prepare()` |
| Cegah XSS (`htmlspecialchars` lewat `e()`) | `includes/functions.php` |
| Pencarian, filter, paginasi (LIMIT / OFFSET) | `barang/index.php` |
| Pola Post/Redirect/Get + pesan flash (session) | `includes/functions.php` |
| Transaksi database (`beginTransaction`, `commit`, `rollBack`) + `FOR UPDATE` | `transaksi/tambah.php` |
| Proteksi hapus data yang masih dipakai | `kategori/hapus.php` |
| Autentikasi: `password_hash` / `password_verify` | `setup.php`, `login.php` |
| Penjaga halaman (semua halaman wajib login) | `config/koneksi.php` -> `wajib_login()` di `includes/auth.php` |
| Keamanan session: `session_regenerate_id`, cookie HttpOnly + SameSite, timeout idle | `config/koneksi.php`, `login.php`, `includes/auth.php` |
| Proteksi CSRF (token di setiap form POST) | `csrf_field()`, `csrf_valid()` di `includes/auth.php` |
| Pembatasan percobaan login (kunci akun sementara) | `login.php` (kolom `gagal_login`, `locked_until`) |

## Fitur

- **Kategori**: tambah, lihat, edit, hapus (tidak bisa dihapus jika masih dipakai barang)
- **Barang**: tambah, lihat, detail + riwayat stok, edit, hapus, cari kode/nama, filter kategori, paginasi
- **Transaksi Stok**: barang masuk/keluar, stok otomatis berubah, stok keluar tidak boleh melebihi stok tersedia
- **Dashboard**: total barang, total kategori, nilai inventory, stok menipis (<= 10), transaksi terakhir

## Keamanan Login

- Semua halaman dilindungi secara default lewat `config/koneksi.php`. Halaman baru otomatis wajib login; halaman yang memang publik harus menulis `define('HALAMAN_PUBLIK', true);` sebelum `require` koneksi.
- Password disimpan sebagai hash (`password_hash`), tidak pernah sebagai teks asli.
- Setelah login, session ID diganti (mencegah session fixation).
- Logout otomatis setelah 30 menit tanpa aktivitas (`SESSION_IDLE` di `includes/auth.php`).
- 5 kali salah password mengunci akun 15 menit (`MAX_GAGAL`, `DURASI_KUNCI`).
- Setiap form POST membawa token CSRF; form tanpa token yang valid ditolak.
- Lupa password? Hapus baris user lewat phpMyAdmin, lalu buka `setup.php` lagi untuk membuat akun baru (hanya bisa jika tabel `users` kosong).

## Masalah Umum

| Masalah | Solusi |
|---|---|
| "Koneksi database gagal" | MySQL belum di-Start, atau database belum diimpor (langkah 2 dan 3) |
| Halaman tanpa gaya / tidak rapi | Tidak ada internet (Bootstrap dari CDN) |
| "Tabel users belum ada" | Impor `database/tambah_users.sql` |
| Selalu kembali ke halaman login | Cookie diblokir browser, atau `BASE_URL` tidak sama dengan nama folder (cookie session dibatasi ke path `BASE_URL`) |
| Link menuju halaman 404 | `BASE_URL` di `config/koneksi.php` tidak sama dengan nama folder |
