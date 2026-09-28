<?php
require __DIR__ . '/../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT b.*, k.nama_kategori FROM barang b
     JOIN kategori k ON k.id = b.kategori_id WHERE b.id = ?'
);
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    set_flash('error', 'Barang tidak ditemukan.');
    redirect('/barang/index.php');
}

$stmt = $pdo->prepare('SELECT * FROM transaksi_stok WHERE barang_id = ? ORDER BY tanggal DESC, id DESC LIMIT 20');
$stmt->execute([$id]);
$riwayat = $stmt->fetchAll();

$judul = 'Detail Barang';
$menu  = 'barang';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Detail Barang</h3>
    <a href="index.php" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card shadow-sm mb-4"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kode Barang</dt><dd class="col-sm-9"><?= e($barang['kode_barang']) ?></dd>
        <dt class="col-sm-3">Nama Barang</dt><dd class="col-sm-9"><?= e($barang['nama_barang']) ?></dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9"><?= e($barang['nama_kategori']) ?></dd>
        <dt class="col-sm-3">Harga</dt><dd class="col-sm-9"><?= rupiah($barang['harga']) ?></dd>
        <dt class="col-sm-3">Stok</dt><dd class="col-sm-9"><?= e($barang['stok']) ?> <?= e($barang['satuan']) ?></dd>
        <dt class="col-sm-3">Lokasi</dt><dd class="col-sm-9"><?= e($barang['lokasi'] ?: '-') ?></dd>
    </dl>
</div></div>

<h5>Riwayat Stok (20 terakhir)</h5>
<div class="card shadow-sm"><div class="table-responsive">
    <table class="table mb-0">
        <thead class="table-light"><tr><th>Tanggal</th><th>Tipe</th><th>Jumlah</th><th>Keterangan</th></tr></thead>
        <tbody>
        <?php if (!$riwayat): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada riwayat.</td></tr>
        <?php endif; ?>
        <?php foreach ($riwayat as $r): ?>
            <tr>
                <td><?= e(date('d/m/Y', strtotime($r['tanggal']))) ?></td>
                <td><span class="badge <?= $r['tipe'] === 'masuk' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= e(ucfirst($r['tipe'])) ?></span></td>
                <td><?= e($r['jumlah']) ?></td>
                <td><?= e($r['keterangan'] ?: '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
