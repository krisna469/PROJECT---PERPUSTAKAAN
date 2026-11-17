<?php
/**
 * Partial form buku (dipakai tambah_buku.php & edit_buku.php)
 * Variabel: $aksi (url), $buku (array nilai), $edit (bool)
 */
?>
<form action="<?= e($aksi) ?>" method="POST" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $buku['id'] ?>"><?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" name="judul" class="form-control" maxlength="150" value="<?= e($buku['judul']) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach (KATEGORI as $k): ?>
                            <option value="<?= e($k) ?>" <?= $buku['kategori'] === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" maxlength="100" value="<?= e($buku['penulis']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= e($buku['tahun_terbit']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok <small class="text-muted">(0 = Dipinjam)</small></label>
                    <input type="number" name="stok" class="form-control" min="0" max="9999" value="<?= e($buku['stok']) ?>" required>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <label class="form-label">Sampul Buku <small class="text-muted">(JPG/PNG, maks 2MB)</small></label>
            <div class="cover-preview mb-2">
                <img id="previewSampul" src="<?= e($buku['preview']) ?>" alt="Preview sampul">
            </div>
            <input type="file" name="sampul" id="inputSampul" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                   <?= $edit ? '' : 'required' ?>>
            <?php if ($edit): ?><div class="form-text">Kosongkan jika tidak ingin mengganti sampul.</div><?php endif; ?>
        </div>
    </div>
    <hr>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
