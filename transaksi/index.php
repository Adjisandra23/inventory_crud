<?php
require __DIR__ . '/../config/koneksi.php';

$tipe    = $_GET['tipe'] ?? '';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;

$whereSql = '';
$params   = [];
if (in_array($tipe, ['masuk', 'keluar'], true)) {
    $whereSql = 'WHERE t.tipe = ?';
    $params[] = $tipe;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi_stok t $whereSql");
$stmt->execute($params);
$total      = (int) $stmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare(
    "SELECT t.*, b.nama_barang FROM transaksi_stok t
     JOIN barang b ON b.id = t.barang_id
     $whereSql
     ORDER BY t.tanggal DESC, t.id DESC
     LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$transaksis = $stmt->fetchAll();

$judul = 'Transaksi Stok';
$menu  = 'transaksi';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Transaksi Stok</h3>
    <a href="tambah.php" class="btn btn-primary">+ Barang Masuk / Keluar</a>
</div>

<form method="GET" class="mb-3 d-flex gap-2">
    <select name="tipe" class="form-select w-auto">
        <option value="">Semua Tipe</option>
        <option value="masuk" <?= $tipe === 'masuk' ? 'selected' : '' ?>>Masuk</option>
        <option value="keluar" <?= $tipe === 'keluar' ? 'selected' : '' ?>>Keluar</option>
    </select>
    <button type="submit" class="btn btn-outline-primary">Filter</button>
</form>

<div class="card shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>Tanggal</th><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Keterangan</th></tr>
        </thead>
        <tbody>
        <?php if (!$transaksis): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
        <?php endif; ?>
        <?php foreach ($transaksis as $t): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($t['tanggal']))) ?></td>
                <td><?= e($t['nama_barang']) ?></td>
                <td><span class="badge <?= $t['tipe'] === 'masuk' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= e(ucfirst($t['tipe'])) ?></span></td>
                <td><?= e($t['jumlah']) ?></td>
                <td><?= e($t['keterangan'] ?: '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

<?php if ($totalPages > 1): ?>
<nav class="mt-3"><ul class="pagination">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
            <a class="page-link" href="?<?= e(http_build_query(['tipe' => $tipe, 'page' => $p])) ?>"><?= $p ?></a>
        </li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
