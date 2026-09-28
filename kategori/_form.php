<?php // Form bersama untuk tambah & edit. Variabel: $data, $errors ?>
<form method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Nama Kategori</label>
        <input type="text" name="nama_kategori" class="form-control <?= isset($errors['nama_kategori']) ? 'is-invalid' : '' ?>"
               value="<?= e($data['nama_kategori']) ?>">
        <?php if (isset($errors['nama_kategori'])): ?><div class="invalid-feedback"><?= e($errors['nama_kategori']) ?></div><?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="form-control"><?= e($data['deskripsi']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="<?= BASE_URL ?>/kategori/index.php" class="btn btn-secondary">Batal</a>
</form>
