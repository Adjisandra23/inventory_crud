<?php
// Koneksi database memakai PDO + session (pesan flash & autentikasi login).

// Ubah jika nama folder proyek di htdocs berbeda.
define('BASE_URL', '/inventory_crud');

// --- Pengaturan session yang aman (harus sebelum session_start) ---
ini_set('session.use_strict_mode', '1');   // tolak session ID yang tidak dikenal server
ini_set('session.use_only_cookies', '1');  // session ID tidak boleh lewat URL
session_name('INVSESSID');
session_set_cookie_params([
    'lifetime' => 0,                        // hilang saat browser ditutup
    'path'     => BASE_URL,
    'httponly' => true,                     // cookie tidak bisa dibaca JavaScript (mengurangi dampak XSS)
    'samesite' => 'Lax',                    // cookie tidak ikut terkirim pada POST lintas-situs
    'secure'   => !empty($_SERVER['HTTPS']), // aktif otomatis bila memakai HTTPS
]);
session_start();

$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = '';   // XAMPP default kosong

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// --- Penjaga login (default: SEMUA halaman wajib login) ---
// Halaman publik (login.php, setup.php) harus menulis
//   define('HALAMAN_PUBLIK', true);
// SEBELUM require file ini.
if (!defined('HALAMAN_PUBLIK')) {
    wajib_login();
}
