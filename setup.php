<?php
// Membuat akun admin PERTAMA lewat browser. Halaman ini otomatis terkunci
// begitu sudah ada satu user di tabel users.
define('HALAMAN_PUBLIK', true);
require __DIR__ . '/config/koneksi.php';

if (hitung_user($pdo) > 0) {
    set_flash('error', 'Setup sudah pernah dilakukan. Silakan login.');
    redirect('/login.php');
}

$errors = [];
$data   = ['username' => '', 'nama_lengkap' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['username']     = post('username');
    $data['nama_lengkap'] = post('nama_lengkap');
    $password             = $_POST['password'] ?? '';
    $konfirmasi           = $_POST['konfirmasi'] ?? '';

    if (!csrf_valid()) {
        $errors['umum'] = 'Permintaan tidak valid. Muat ulang halaman lalu coba lagi.';
    }
    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $data['username'])) {
        $errors['username'] = 'Username 3-50 karakter: huruf, angka, titik, garis bawah, atau strip.';
    }
    if ($data['nama_lengkap'] === '' || strlen($data['nama_lengkap']) > 100) {
        $errors['nama_lengkap'] = 'Nama lengkap wajib diisi (maksimal 100 karakter).';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter.';
    } elseif (strlen($password) > 72) {
        $errors['password'] = 'Password maksimal 72 karakter.';   // batas bcrypt
    } elseif ($password !== $konfirmasi) {
        $errors['konfirmasi'] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (username, nama_lengkap, password) VALUES (?, ?, ?)');
        $stmt->execute([$data['username'], $data['nama_lengkap'], password_hash($password, PASSWORD_DEFAULT)]);
        set_flash('success', 'Akun admin berhasil dibuat. Silakan login.');
        redirect('/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Admin - Sistem Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <div class="fs-1">🔐</div>
                <h4 class="mb-0">Buat Akun Admin Pertama</h4>
                <div class="text-muted">Halaman ini hanya muncul sekali, saat belum ada user.</div>
            </div>

            <?php if (isset($errors['umum'])): ?>
                <div class="alert alert-danger"><?= e($errors['umum']) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm"><div class="card-body p-4">
                <form method="POST" autocomplete="off">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" value="<?= e($data['nama_lengkap']) ?>">
                        <?php if (isset($errors['nama_lengkap'])): ?><div class="invalid-feedback"><?= e($errors['nama_lengkap']) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" value="<?= e($data['username']) ?>">
                        <?php if (isset($errors['username'])): ?><div class="invalid-feedback"><?= e($errors['username']) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="konfirmasi" class="form-control <?= isset($errors['konfirmasi']) ? 'is-invalid' : '' ?>">
                        <?php if (isset($errors['konfirmasi'])): ?><div class="invalid-feedback"><?= e($errors['konfirmasi']) ?></div><?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
</body>
</html>
