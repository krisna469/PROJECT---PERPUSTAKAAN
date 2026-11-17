<?php
require_once 'config/koneksi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('tambah_buku.php');
}
cek_csrf();

$data = [
    'judul'        => trim($_POST['judul'] ?? ''),
    'kategori'     => $_POST['kategori'] ?? '',
    'penulis'      => trim($_POST['penulis'] ?? ''),
    'tahun'        => trim($_POST['tahun'] ?? ''),
    'stok'         => trim($_POST['stok'] ?? ''),
];

$error = validasi_buku($data);
[$nama_file, $err_upload] = proses_upload_sampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE], true);
if ($err_upload) {
    $error[] = $err_upload;
}

if ($error) {
    hapus_file_sampul($nama_file);
    $_SESSION['old'] = [
        'judul' => $data['judul'], 'kategori' => $data['kategori'], 'penulis' => $data['penulis'],
        'tahun_terbit' => $data['tahun'], 'stok' => $data['stok'],
    ];
    set_flash('danger', implode(' ', $error));
    redirect('tambah_buku.php');
}

$tahun = (int) $data['tahun'];
$stok  = (int) $data['stok'];
$stmt = $koneksi->prepare('INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->bind_param('sssiis', $data['judul'], $data['kategori'], $data['penulis'], $tahun, $stok, $nama_file);
$stmt->execute();
$stmt->close();

set_flash('success', 'Buku "' . $data['judul'] . '" berhasil ditambahkan.');
redirect('dashboard.php');
