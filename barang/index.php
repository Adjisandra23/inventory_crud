<?php
require __DIR__ . '/../config/koneksi.php';

// Parameter pencarian, filter, dan halaman dari URL (GET)
$q          = trim($_GET['q'] ?? '');
$kategoriId = (int) ($_GET['kategori_id'] ?? 0);
$page       = max(1, (int) ($_GET['page'] ?? 1));
$perPage    = 10;

// Susun WHERE secara dinamis
$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(b.nama_barang LIKE ? OR b.kode_barang LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($kategoriId > 0) {
    $where[]  = 'b.kategori_id = ?';
    $params[] = $kategoriId;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung total untuk paginasi
$stmt = $pdo->prepare("SELECT COUNT(*) FROM barang b $whereSql");
$stmt->execute($params);
$total      = (int) $stmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// Ambil data halaman ini (perPage & offset sudah di-cast int, aman)
$stmt = $pdo->prepare(
    "SELECT b.*, k.nama_kategori
     FROM barang b
     JOIN kategori k ON k.id = b.kategori_id
     $whereSql
     ORDER BY b.id DESC
     LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$barangs = $stmt->fetchAll();

$kategoris = $pdo->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();

$judul = 'Barang';
$menu  = 'barang';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Data Barang</h3>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Barang</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" class="form-control" placeholder="Cari kode / nama barang..." value="<?= e($q) ?>">
    </div>
    <div class="col-md-4">
        <select name="kategori_id" class="form-select">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoris as $k): ?>
                <option value="<?= (int) $k['id'] ?>" <?= $kategoriId === (int) $k['id'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
        <a href="index.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>#</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Harga</th><th>Stok</th><th class="text-end">Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$barangs): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($barangs as $i => $b): ?>
            <tr>
                <td><?= $offset + $i + 1 ?></td>
                <td><?= e($b['kode_barang']) ?></td>
                <td><?= e($b['nama_barang']) ?></td>
                <td><?= e($b['nama_kategori']) ?></td>
                <td><?= rupiah($b['harga']) ?></td>
                <td><span class="badge <?= $b['stok'] <= 10 ? 'text-bg-danger' : 'text-bg-success' ?>"><?= e($b['stok']) ?> <?= e($b['satuan']) ?></span></td>
                <td class="text-end">
                    <a href="detail.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                    <a href="edit.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form action="hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus barang ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>

<?php if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++):
            $query = http_build_query(['q' => $q, 'kategori_id' => $kategoriId ?: '', 'page' => $p]); ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= e($query) ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
