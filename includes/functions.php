<?php
// Fungsi bantu yang dipakai di banyak halaman.

// Cegah XSS: selalu bungkus output dengan e()
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka)
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

function redirect($url)
{
    header('Location: ' . BASE_URL . $url);
    exit;
}

// Pesan flash: disimpan di session, tampil sekali lalu hilang
function set_flash($tipe, $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function get_flash()
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// Ambil nilai POST dengan aman (trim)
function post($nama, $default = '')
{
    return isset($_POST[$nama]) ? trim($_POST[$nama]) : $default;
}
