<?php // Variabel: $data, $errors, $kategoris, $mode ('tambah' atau 'edit') ?>
<form method="POST">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control <?= isset($errors['kode_barang']) ? 'is-invalid' : '' ?>"
                   value="<?= e($data['kode_barang']) ?>">
            <?php if (isset($errors['kode_barang'])): ?><div class="invalid-feedback"><?= e($errors['kode_barang']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control <?= isset($errors['nama_barang']) ? 'is-invalid' : '' ?>"
                   value="<?= e($data['nama_barang']) ?>">
            <?php if (isset($errors['nama_barang'])): ?><div class="invalid-feedback"><?= e($errors['nama_barang']) ?></div><?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select <?= isset($errors['kategori_id']) ? 'is-invalid' : '' ?>">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                    <option value="<?= (int) $k['id'] ?>" <?= (string) $data['kategori_id'] === (string) $k['id'] ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori_id'])): ?><div class="invalid-feedback"><?= e($errors['kategori_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="harga" min="0" class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>"
                   value="<?= e($data['harga']) ?>">
            <?php if (isset($errors['harga'])): ?><div class="invalid-feedback"><?= e($errors['harga']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control <?= isset($errors['satuan']) ? 'is-invalid' : '' ?>"
                   value="<?= e($data['satuan']) ?>">
            <?php if (isset($errors['satuan'])): ?><div class="invalid-feedback"><?= e($errors['satuan']) ?></div><?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Stok <?= $mode === 'edit' ? '(ubah lewat Transaksi Stok)' : 'Awal' ?></label>
            <?php if ($mode === 'edit'): ?>
                <input type="number" class="form-control" value="<?= e($data['stok']) ?>" disabled>
            <?php else: ?>
                <input type="number" name="stok" min="0" class="form-control <?= isset($errors['stok']) ? 'is-invalid' : '' ?>"
                       value="<?= e($data['stok']) ?>">
                <?php if (isset($errors['stok'])): ?><div class="invalid-feedback"><?= e($errors['stok']) ?></div><?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label">Lokasi Penyimpanan</label>
            <input type="text" name="lokasi" class="form-control" value="<?= e($data['lokasi']) ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= BASE_URL ?>/barang/index.php" class="btn btn-secondary">Batal</a>
</form>
