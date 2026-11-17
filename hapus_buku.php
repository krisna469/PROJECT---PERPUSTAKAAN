<?php
require_once 'config/koneksi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}
cek_csrf();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $koneksi->prepare('SELECT judul, sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($buku) {
    $stmt = $koneksi->prepare('DELETE FROM buku WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    hapus_file_sampul($buku['sampul']); // unlink file gambar
    set_flash('success', 'Buku "' . $buku['judul'] . '" beserta sampulnya berhasil dihapus.');
} else {
    set_flash('danger', 'Data buku tidak ditemukan.');
}
redirect('dashboard.php');
