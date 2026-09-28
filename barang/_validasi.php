<?php
// Dipakai tambah.php dan edit.php.
// Input : $pdo, $data (isi form), $mode, $idSendiri (untuk edit)
// Output: $errors
$errors = [];

if ($data['kode_barang'] === '') {
    $errors['kode_barang'] = 'Kode barang wajib diisi.';
} elseif (strlen($data['kode_barang']) > 50) {
    $errors['kode_barang'] = 'Maksimal 50 karakter.';
} else {
    $cek = $pdo->prepare('SELECT COUNT(*) FROM barang WHERE kode_barang = ? AND id != ?');
    $cek->execute([$data['kode_barang'], $idSendiri]);
    if ($cek->fetchColumn() > 0) {
        $errors['kode_barang'] = 'Kode barang sudah dipakai.';
    }
}

if ($data['nama_barang'] === '') {
    $errors['nama_barang'] = 'Nama barang wajib diisi.';
} elseif (strlen($data['nama_barang']) > 150) {
    $errors['nama_barang'] = 'Maksimal 150 karakter.';
}

if ($data['kategori_id'] === '') {
    $errors['kategori_id'] = 'Kategori wajib dipilih.';
} else {
    $cek = $pdo->prepare('SELECT COUNT(*) FROM kategori WHERE id = ?');
    $cek->execute([(int) $data['kategori_id']]);
    if ($cek->fetchColumn() == 0) {
        $errors['kategori_id'] = 'Kategori tidak valid.';
    }
}

if ($data['harga'] === '' || !ctype_digit($data['harga'])) {
    $errors['harga'] = 'Harga harus berupa angka 0 atau lebih.';
}

if ($data['satuan'] === '') {
    $errors['satuan'] = 'Satuan wajib diisi.';
} elseif (strlen($data['satuan']) > 30) {
    $errors['satuan'] = 'Maksimal 30 karakter.';
}

if ($mode === 'tambah' && ($data['stok'] === '' || !ctype_digit($data['stok']))) {
    $errors['stok'] = 'Stok harus berupa angka 0 atau lebih.';
}
