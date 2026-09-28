-- Import file ini lewat phpMyAdmin (tab Import) atau: mysql -u root < inventory_db.sql
CREATE DATABASE IF NOT EXISTS inventory_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_db;

DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS transaksi_stok;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS kategori;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,            -- hash dari password_hash(), bukan teks asli
    gagal_login TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- Akun admin pertama dibuat lewat halaman setup.php (bukan di SQL, agar tidak ada password bawaan).

CREATE TABLE kategori (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE barang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori_id INT UNSIGNED NOT NULL,
    harga BIGINT UNSIGNED NOT NULL DEFAULT 0,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    satuan VARCHAR(30) NOT NULL DEFAULT 'pcs',
    lokasi VARCHAR(150) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_barang_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE transaksi_stok (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    barang_id INT UNSIGNED NOT NULL,
    tipe ENUM('masuk','keluar') NOT NULL,
    jumlah INT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_barang FOREIGN KEY (barang_id)
        REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Data contoh
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik', 'Perangkat dan aksesoris elektronik'),
('Alat Tulis Kantor', 'Perlengkapan tulis dan kantor'),
('Minuman', 'Minuman kemasan');

INSERT INTO barang (kode_barang, nama_barang, kategori_id, harga, stok, satuan, lokasi) VALUES
('BRG-001', 'Mouse Wireless',      1, 85000,  25,  'pcs',   'Rak A1'),
('BRG-002', 'Keyboard Mechanical', 1, 350000, 8,   'pcs',   'Rak A2'),
('BRG-003', 'Buku Tulis A5',       2, 6000,   120, 'pcs',   'Rak B1'),
('BRG-004', 'Pulpen Hitam',        2, 3500,   5,   'pcs',   'Rak B2'),
('BRG-005', 'Air Mineral 600ml',   3, 3000,   200, 'botol', 'Gudang C'),
('BRG-006', 'Teh Botol',           3, 5000,   60,  'botol', 'Gudang C');
