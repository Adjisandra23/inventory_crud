<?php
// Logout hanya lewat POST (dengan token CSRF, diperiksa otomatis oleh penjaga login),
// supaya pihak lain tidak bisa memaksa Anda logout lewat link/gambar tersembunyi.
require __DIR__ . '/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

keluarkan_user();
set_flash('success', 'Anda berhasil keluar.');
redirect('/login.php');
