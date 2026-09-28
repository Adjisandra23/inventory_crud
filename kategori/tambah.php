<?php
require __DIR__ . '/../config/koneksi.php';

$errors = [];
$data   = ['nama_kategori' => '', 'deskripsi' => ''];

// CREATE: proses form saat disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_kategori'] = post('nama_kategori');
    $data['deskripsi']     = post('deskripsi');

    // Validasi
    if ($data['nama_kategori'] === '') {
        $errors['nama_kategori'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($data['nama_kategori']) > 100) {
        $errors['nama_kategori'] = 'Maksimal 100 karakter.';
    } else {
        $cek = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE nama_kategori = ?');
        $cek->execute([$data['nama_kategori']]);
        if ($cek->fetchColumn() > 0) {
            $errors['nama_kategori'] = 'Nama kategori sudah ada.';
        }
    }

    // Simpan jika tidak ada error (prepared statement mencegah SQL injection)
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)');
        $stmt->execute([$data['nama_kategori'], $data['deskripsi'] ?: null]);
        set_flash('success', 'Kategori berhasil ditambahkan.');
        redirect('/kategori/index.php');
    }
}

$judul = 'Tambah Kategori';
$menu  = 'kategori';
require __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Tambah Kategori</h3>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
