<?php
// Fungsi autentikasi: session login, timeout, CSRF, dan pembatasan percobaan login.

const SESSION_IDLE = 1800;  // detik tanpa aktivitas sebelum otomatis logout (30 menit)
const MAX_GAGAL    = 5;     // gagal login berturut-turut sebelum akun dikunci
const DURASI_KUNCI = 900;   // lama akun dikunci dalam detik (15 menit)

function sudah_login()
{
    return isset($_SESSION['user_id']);
}

// Dipanggil otomatis dari koneksi.php di setiap halaman yang bukan publik.
function wajib_login()
{
    if (!sudah_login()) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login.php');
    }

    // Timeout: keluar otomatis jika terlalu lama tidak aktif
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_IDLE) {
        keluarkan_user();
        set_flash('error', 'Sesi berakhir karena tidak ada aktivitas. Silakan login kembali.');
        redirect('/login.php');
    }
    $_SESSION['last_activity'] = time();

    // Cegah browser menyimpan halaman (tombol Back setelah logout tidak menampilkan data lagi)
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    // Semua request POST wajib membawa token CSRF yang valid
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_valid()) {
        set_flash('error', 'Permintaan tidak valid atau formulir sudah kedaluwarsa. Silakan ulangi.');
        redirect('/index.php');
    }
}

// Hapus data login dan ganti session ID. Session tetap ada agar pesan flash bisa tampil.
function keluarkan_user()
{
    $_SESSION = [];
    session_regenerate_id(true);
}

// ---------- CSRF ----------
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Taruh di dalam setiap <form method="POST">
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid()
{
    $kirim = $_POST['csrf_token'] ?? '';
    return is_string($kirim)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $kirim);
}

// ---------- Tabel users ----------
// Menghitung user; jika tabel belum dibuat, tampilkan petunjuk yang jelas.
function hitung_user($pdo)
{
    try {
        return (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    } catch (PDOException $e) {
        die('Tabel <b>users</b> belum ada. Impor file <code>database/tambah_users.sql</code> lewat phpMyAdmin terlebih dahulu.');
    }
}
