<?php
require __DIR__ . '/../config/koneksi.php';

// DELETE: hanya menerima POST (bukan GET) agar tidak terhapus lewat klik link/crawler
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/kategori/index.php');
}

$id = (int) ($_POST['id'] ?? 0);

// Cegah hapus jika masih dipakai barang
$cek = $pdo->prepare('SELECT COUNT(*) FROM barang WHERE kategori_id = ?');
$cek->execute([$id]);

if ($cek->fetchColumn() > 0) {
    set_flash('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh barang.');
} else {
    $stmt = $pdo->prepare('DELETE FROM kategori WHERE id = ?');
    $stmt->execute([$id]);
    set_flash('success', 'Kategori berhasil dihapus.');
}
redirect('/kategori/index.php');
