<?php
require __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/barang/index.php');
}

$id = (int) ($_POST['id'] ?? 0);

// Riwayat transaksi ikut terhapus karena ON DELETE CASCADE di database
$stmt = $pdo->prepare('DELETE FROM barang WHERE id = ?');
$stmt->execute([$id]);

set_flash('success', 'Barang berhasil dihapus.');
redirect('/barang/index.php');
