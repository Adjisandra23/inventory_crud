<?php
define('HALAMAN_PUBLIK', true);
require __DIR__ . '/config/koneksi.php';

// Sudah login? Tidak perlu melihat halaman login lagi.
if (sudah_login()) {
    redirect('/index.php');
}

// Belum ada user sama sekali: arahkan ke pembuatan admin pertama.
if (hitung_user($pdo) === 0) {
    redirect('/setup.php');
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username');
    $password = $_POST['password'] ?? '';   // password tidak di-trim

    if (!csrf_valid()) {
        $error = 'Permintaan tidak valid. Muat ulang halaman lalu coba lagi.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $terkunciSampai = $user && $user['locked_until'] ? strtotime($user['locked_until']) : 0;

        if ($terkunciSampai > time()) {
            $menit = (int) ceil(($terkunciSampai - time()) / 60);
            $error = "Terlalu banyak percobaan gagal. Coba lagi dalam $menit menit.";
        } else {
            // Bila username tidak ada, tetap jalankan password_verify agar waktu respons
            // tidak membocorkan apakah username tersebut terdaftar.
            $hash  = $user ? $user['password'] : password_hash('dummy-password', PASSWORD_DEFAULT);
            $cocok = password_verify($password, $hash);

            if ($user && $cocok) {
                // Login berhasil: ganti session ID (cegah session fixation)
                session_regenerate_id(true);
                unset($_SESSION['csrf_token']);
                $_SESSION['user_id']       = (int) $user['id'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['nama']          = $user['nama_lengkap'];
                $_SESSION['last_activity'] = time();

                $upd = $pdo->prepare('UPDATE users SET gagal_login = 0, locked_until = NULL WHERE id = ?');
                $upd->execute([$user['id']]);

                // Perbarui hash otomatis jika algoritma default PHP sudah berganti
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $re = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $re->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                }

                set_flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
                redirect('/index.php');
            }

            // Login gagal: hitung percobaan, kunci akun jika melewati batas
            if ($user) {
                $gagal = (int) $user['gagal_login'] + 1;
                $kunci = null;
                if ($gagal >= MAX_GAGAL) {
                    $kunci = date('Y-m-d H:i:s', time() + DURASI_KUNCI);
                    $gagal = 0;
                }
                $upd = $pdo->prepare('UPDATE users SET gagal_login = ?, locked_until = ? WHERE id = ?');
                $upd->execute([$gagal, $kunci, $user['id']]);
            }
            // Pesan sengaja sama untuk "user tidak ada" dan "password salah"
            $error = 'Username atau password salah.';
        }
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <div class="fs-1">📦</div>
                <h4 class="mb-0">Sistem Inventory</h4>
                <div class="text-muted">Silakan login untuk melanjutkan</div>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['tipe'] === 'success' ? 'success' : 'danger' ?>"><?= e($flash['pesan']) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm"><div class="card-body p-4">
                <form method="POST" autocomplete="off">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= e($username) ?>" maxlength="50" autofocus required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
</body>
</html>
