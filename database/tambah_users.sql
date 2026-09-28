-- Untuk database yang SUDAH berisi data: impor file ini saja (data lama tidak terhapus).
-- Jangan impor ulang inventory_db.sql karena file itu menghapus semua tabel lebih dulu.
USE inventory_db;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,            -- hash dari password_hash(), bukan teks asli
    gagal_login TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- Akun admin pertama dibuat lewat http://localhost/inventory_crud/setup.php
