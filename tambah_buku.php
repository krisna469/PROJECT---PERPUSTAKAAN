<?php
require_once 'config/koneksi.php';
wajib_login();
$judul_halaman = 'Tambah Buku';

// Isi ulang form jika validasi gagal
$buku = $_SESSION['old'] ?? ['judul' => '', 'kategori' => '', 'penulis' => '', 'tahun_terbit' => '', 'stok' => 1];
unset($_SESSION['old']);
$buku['preview'] = 'assets/img/no-cover.png';
$aksi = 'proses_tambah.php';
$edit = false;

require 'config/header.php';
?>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2"></i>Tambah Buku Baru</h5>
        <?php tampil_flash(); ?>
        <?php require 'config/form_buku.php'; ?>
    </div>
</div>
<?php require 'config/footer.php'; ?>
