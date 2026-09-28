<?php
require __DIR__ . '/../config/koneksi.php';

// READ: ambil semua kategori + jumlah barang tiap kategori
$kategoris = $pdo->query(
    'SELECT k.*, COUNT(b.id) AS jumlah_barang
     FROM kategori k
     LEFT JOIN barang b ON b.kategori_id = k.id
     GROUP BY k.id
     ORDER BY k.id DESC'
)->fetchAll();

$judul = 'Kategori';
$menu  = 'kategori';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Data Kategori</h3>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Kategori</a>
</div>

<div class="card shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr><th>#</th><th>Nama Kategori</th><th>Deskripsi</th><th>Jumlah Barang</th><th class="text-end">Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (!$kategoris): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
        <?php endif; ?>
        <?php foreach ($kategoris as $i => $k): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($k['nama_kategori']) ?></td>
                <td><?= e($k['deskripsi'] ?: '-') ?></td>
                <td><span class="badge text-bg-info"><?= e($k['jumlah_barang']) ?></span></td>
                <td class="text-end">
                    <a href="edit.php?id=<?= (int) $k['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form action="hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
