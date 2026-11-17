<?php
require_once 'config/koneksi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}
cek_csrf();

$id = (int) ($_POST['id'] ?? 0);

// Ambil data lama (untuk nama file sampul lama)
$stmt = $koneksi->prepare('SELECT sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$lama) {
    set_flash('danger', 'Data buku tidak ditemukan.');
    redirect('dashboard.php');
}

$data = [
    'judul'    => trim($_POST['judul'] ?? ''),
    'kategori' => $_POST['kategori'] ?? '',
    'penulis'  => trim($_POST['penulis'] ?? ''),
    'tahun'    => trim($_POST['tahun'] ?? ''),
    'stok'     => trim($_POST['stok'] ?? ''),
];

$error = validasi_buku($data);
[$file_baru, $err_upload] = proses_upload_sampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE], false);
if ($err_upload) {
    $error[] = $err_upload;
}

if ($error) {
    hapus_file_sampul($file_baru);
    set_flash('danger', implode(' ', $error));
    redirect('edit_buku.php?id=' . $id);
}

$tahun = (int) $data['tahun'];
$stok  = (int) $data['stok'];
$sampul = $file_baru ?? $lama['sampul'];

$stmt = $koneksi->prepare('UPDATE buku SET judul=?, kategori=?, penulis=?, tahun_terbit=?, stok=?, sampul=? WHERE id=?');
$stmt->bind_param('sssiisi', $data['judul'], $data['kategori'], $data['penulis'], $tahun, $stok, $sampul, $id);
$stmt->execute();
$stmt->close();

// Jika sampul diganti, hapus file lama dari server
if ($file_baru) {
    hapus_file_sampul($lama['sampul']);
}

set_flash('success', 'Data buku berhasil diperbarui.');
redirect('dashboard.php');
