<?php
require __DIR__ . '/../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

// Ambil data lama
$stmt = $pdo->prepare('SELECT * FROM kategori WHERE id = ?');
$stmt->execute([$id]);
$kategori = $stmt->fetch();

if (!$kategori) {
    set_flash('error', 'Kategori tidak ditemukan.');
    redirect('/kategori/index.php');
}

$errors = [];
$data   = ['nama_kategori' => $kategori['nama_kategori'], 'deskripsi' => (string) $kategori['deskripsi']];

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_kategori'] = post('nama_kategori');
    $data['deskripsi']     = post('deskripsi');

    if ($data['nama_kategori'] === '') {
        $errors['nama_kategori'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($data['nama_kategori']) > 100) {
        $errors['nama_kategori'] = 'Maksimal 100 karakter.';
    } else {
        // Unik, kecuali untuk dirinya sendiri (id != ?)
        $cek = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE nama_kategori = ? AND id != ?');
        $cek->execute([$data['nama_kategori'], $id]);
        if ($cek->fetchColumn() > 0) {
            $errors['nama_kategori'] = 'Nama kategori sudah ada.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id = ?');
        $stmt->execute([$data['nama_kategori'], $data['deskripsi'] ?: null, $id]);
        set_flash('success', 'Kategori berhasil diperbarui.');
        redirect('/kategori/index.php');
    }
}

$judul = 'Edit Kategori';
$menu  = 'kategori';
require __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Edit Kategori</h3>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
