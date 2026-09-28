<?php
require __DIR__ . '/config/koneksi.php';

$totalBarang   = $pdo->query('SELECT COUNT(*) FROM barang')->fetchColumn();
$totalKategori = $pdo->query('SELECT COUNT(*) FROM kategori')->fetchColumn();
$nilai         = $pdo->query('SELECT COALESCE(SUM(harga * stok), 0) FROM barang')->fetchColumn();

$stokMenipis = $pdo->query(
    'SELECT b.*, k.nama_kategori FROM barang b
     JOIN kategori k ON k.id = b.kategori_id
     WHERE b.stok <= 10 ORDER BY b.stok ASC'
)->fetchAll();

$transaksiBaru = $pdo->query(
    'SELECT t.*, b.nama_barang FROM transaksi_stok t
     JOIN barang b ON b.id = t.barang_id
     ORDER BY t.id DESC LIMIT 5'
)->fetchAll();

$judul = 'Dashboard';
$menu  = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<h3 class="mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">Total Barang</div><div class="fs-2 fw-bold"><?= e($totalBarang) ?></div></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">Total Kategori</div><div class="fs-2 fw-bold"><?= e($totalKategori) ?></div></div></div></div>
    <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">Nilai Inventory</div><div class="fs-2 fw-bold"><?= rupiah($nilai) ?></div></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-warning-subtle fw-semibold">⚠️ Stok Menipis (≤ 10)</div>
            <ul class="list-group list-group-flush">
                <?php if (!$stokMenipis): ?>
                    <li class="list-group-item text-muted">Semua stok aman.</li>
                <?php endif; ?>
                <?php foreach ($stokMenipis as $b): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($b['nama_barang']) ?> <small class="text-muted">(<?= e($b['nama_kategori']) ?>)</small></span>
                        <span class="badge text-bg-danger"><?= e($b['stok']) ?> <?= e($b['satuan']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">🕒 Transaksi Terakhir</div>
            <ul class="list-group list-group-flush">
                <?php if (!$transaksiBaru): ?>
                    <li class="list-group-item text-muted">Belum ada transaksi.</li>
                <?php endif; ?>
                <?php foreach ($transaksiBaru as $t): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= e($t['nama_barang']) ?></span>
                        <span class="badge <?= $t['tipe'] === 'masuk' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                            <?= $t['tipe'] === 'masuk' ? '+' : '-' ?><?= e($t['jumlah']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
