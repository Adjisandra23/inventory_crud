<?php
require __DIR__ . '/../config/koneksi.php';

$errors = [];
$data   = ['barang_id' => '', 'tipe' => 'masuk', 'jumlah' => '1', 'tanggal' => date('Y-m-d'), 'keterangan' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $kolom => $nilai) {
        $data[$kolom] = post($kolom);
    }

    // Validasi
    if ($data['barang_id'] === '' || !ctype_digit($data['barang_id'])) {
        $errors['barang_id'] = 'Barang wajib dipilih.';
    }
    if (!in_array($data['tipe'], ['masuk', 'keluar'], true)) {
        $errors['tipe'] = 'Tipe tidak valid.';
    }
    if (!ctype_digit($data['jumlah']) || (int) $data['jumlah'] < 1) {
        $errors['jumlah'] = 'Jumlah minimal 1.';
    }
    $tgl = DateTime::createFromFormat('Y-m-d', $data['tanggal']);
    if (!$tgl || $tgl->format('Y-m-d') !== $data['tanggal']) {
        $errors['tanggal'] = 'Tanggal tidak valid.';
    }
    if (strlen($data['keterangan']) > 255) {
        $errors['keterangan'] = 'Maksimal 255 karakter.';
    }

    if (!$errors) {
        // Transaksi database: update stok + catat riwayat harus berhasil bersamaan
        try {
            $pdo->beginTransaction();

            // FOR UPDATE mengunci baris barang agar stok tidak bentrok
            $stmt = $pdo->prepare('SELECT * FROM barang WHERE id = ? FOR UPDATE');
            $stmt->execute([(int) $data['barang_id']]);
            $barang = $stmt->fetch();

            if (!$barang) {
                throw new RuntimeException('Barang tidak ditemukan.');
            }

            $jumlah = (int) $data['jumlah'];

            if ($data['tipe'] === 'keluar') {
                if ($jumlah > $barang['stok']) {
                    throw new RuntimeException("Stok tidak cukup. Stok {$barang['nama_barang']} tersisa {$barang['stok']} {$barang['satuan']}.");
                }
                $upd = $pdo->prepare('UPDATE barang SET stok = stok - ? WHERE id = ?');
            } else {
                $upd = $pdo->prepare('UPDATE barang SET stok = stok + ? WHERE id = ?');
            }
            $upd->execute([$jumlah, $barang['id']]);

            $ins = $pdo->prepare(
                'INSERT INTO transaksi_stok (barang_id, tipe, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)'
            );
            $ins->execute([$barang['id'], $data['tipe'], $jumlah, $data['tanggal'], $data['keterangan'] ?: null]);

            $pdo->commit();
            set_flash('success', 'Transaksi stok berhasil disimpan.');
            redirect('/transaksi/index.php');
        } catch (RuntimeException $ex) {
            $pdo->rollBack();
            set_flash('error', $ex->getMessage());
        }
    }
}

$barangs = $pdo->query('SELECT id, kode_barang, nama_barang, stok, satuan FROM barang ORDER BY nama_barang')->fetchAll();

$judul = 'Transaksi Stok Baru';
$menu  = 'transaksi';
require __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Barang Masuk / Keluar</h3>
<div class="card shadow-sm"><div class="card-body">
<form method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Barang</label>
        <select name="barang_id" class="form-select <?= isset($errors['barang_id']) ? 'is-invalid' : '' ?>">
            <option value="">-- Pilih Barang --</option>
            <?php foreach ($barangs as $b): ?>
                <option value="<?= (int) $b['id'] ?>" <?= $data['barang_id'] === (string) $b['id'] ? 'selected' : '' ?>>
                    <?= e($b['kode_barang']) ?> - <?= e($b['nama_barang']) ?> (stok: <?= e($b['stok']) ?> <?= e($b['satuan']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['barang_id'])): ?><div class="invalid-feedback"><?= e($errors['barang_id']) ?></div><?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Tipe</label>
            <select name="tipe" class="form-select <?= isset($errors['tipe']) ? 'is-invalid' : '' ?>">
                <option value="masuk" <?= $data['tipe'] === 'masuk' ? 'selected' : '' ?>>Barang Masuk</option>
                <option value="keluar" <?= $data['tipe'] === 'keluar' ? 'selected' : '' ?>>Barang Keluar</option>
            </select>
            <?php if (isset($errors['tipe'])): ?><div class="invalid-feedback"><?= e($errors['tipe']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" min="1" class="form-control <?= isset($errors['jumlah']) ? 'is-invalid' : '' ?>" value="<?= e($data['jumlah']) ?>">
            <?php if (isset($errors['jumlah'])): ?><div class="invalid-feedback"><?= e($errors['jumlah']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control <?= isset($errors['tanggal']) ? 'is-invalid' : '' ?>" value="<?= e($data['tanggal']) ?>">
            <?php if (isset($errors['tanggal'])): ?><div class="invalid-feedback"><?= e($errors['tanggal']) ?></div><?php endif; ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control <?= isset($errors['keterangan']) ? 'is-invalid' : '' ?>" value="<?= e($data['keterangan']) ?>">
        <?php if (isset($errors['keterangan'])): ?><div class="invalid-feedback"><?= e($errors['keterangan']) ?></div><?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
