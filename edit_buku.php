<?php
require_once 'config/koneksi.php';
wajib_login();
$judul_halaman = 'Edit Buku';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $koneksi->prepare('SELECT * FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    set_flash('danger', 'Data buku tidak ditemukan.');
    redirect('dashboard.php');
}
$buku['preview'] = is_file(UPLOAD_DIR . $buku['sampul']) ? 'uploads/' . $buku['sampul'] : 'assets/img/no-cover.png';
$aksi = 'proses_edit.php';
$edit = true;

require 'config/header.php';
?>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Edit Buku</h5>
        <?php tampil_flash(); ?>
        <?php require 'config/form_buku.php'; ?>
    </div>
</div>
<?php require 'config/footer.php'; ?>
