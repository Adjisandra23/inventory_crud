<?php
require __DIR__ . '/../config/koneksi.php';

$mode      = 'tambah';
$idSendiri = 0;
$errors    = [];
$data      = ['kode_barang' => '', 'nama_barang' => '', 'kategori_id' => '', 'harga' => '0',
              'stok' => '0', 'satuan' => 'pcs', 'lokasi' => ''];

$kategoris = $pdo->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $kolom => $nilai) {
        $data[$kolom] = post($kolom);
    }

    require __DIR__ . '/_validasi.php';   // mengisi $errors

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO barang (kode_barang, nama_barang, kategori_id, harga, stok, satuan, lokasi)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['kode_barang'], $data['nama_barang'], (int) $data['kategori_id'],
            (int) $data['harga'], (int) $data['stok'], $data['satuan'], $data['lokasi'] ?: null,
        ]);
        set_flash('success', 'Barang berhasil ditambahkan.');
        redirect('/barang/index.php');
    }
}

$judul = 'Tambah Barang';
$menu  = 'barang';
require __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Tambah Barang</h3>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
