<?php
require __DIR__ . '/../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM barang WHERE id = ?');
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    set_flash('error', 'Barang tidak ditemukan.');
    redirect('/barang/index.php');
}

$mode      = 'edit';
$idSendiri = $id;
$errors    = [];
$data      = [
    'kode_barang' => $barang['kode_barang'], 'nama_barang' => $barang['nama_barang'],
    'kategori_id' => (string) $barang['kategori_id'], 'harga' => (string) $barang['harga'],
    'stok' => (string) $barang['stok'], 'satuan' => $barang['satuan'], 'lokasi' => (string) $barang['lokasi'],
];

$kategoris = $pdo->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();

// UPDATE (stok tidak ikut diubah; ubah stok lewat Transaksi Stok)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['kode_barang', 'nama_barang', 'kategori_id', 'harga', 'satuan', 'lokasi'] as $kolom) {
        $data[$kolom] = post($kolom);
    }

    require __DIR__ . '/_validasi.php';

    if (!$errors) {
        $stmt = $pdo->prepare(
            'UPDATE barang SET kode_barang = ?, nama_barang = ?, kategori_id = ?,
                    harga = ?, satuan = ?, lokasi = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['kode_barang'], $data['nama_barang'], (int) $data['kategori_id'],
            (int) $data['harga'], $data['satuan'], $data['lokasi'] ?: null, $id,
        ]);
        set_flash('success', 'Barang berhasil diperbarui.');
        redirect('/barang/index.php');
    }
}

$judul = 'Edit Barang';
$menu  = 'barang';
require __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Edit Barang</h3>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
