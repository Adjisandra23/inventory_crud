<?php
// Variabel yang diisi halaman pemanggil: $judul, $menu
$judul = $judul ?? 'Inventory';
$menu  = $menu ?? '';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> - Sistem Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">📦 Inventory</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= $menu === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= $menu === 'kategori' ? 'active' : '' ?>" href="<?= BASE_URL ?>/kategori/index.php">Kategori</a></li>
                <li class="nav-item"><a class="nav-link <?= $menu === 'barang' ? 'active' : '' ?>" href="<?= BASE_URL ?>/barang/index.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link <?= $menu === 'transaksi' ? 'active' : '' ?>" href="<?= BASE_URL ?>/transaksi/index.php">Transaksi Stok</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2 text-white">
                <span class="small">👤 <?= e($_SESSION['nama'] ?? '') ?></span>
                <form action="<?= BASE_URL ?>/logout.php" method="POST" class="mb-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>
<div class="container pb-5">
<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['tipe'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= e($flash['pesan']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
