<?php
// Layout atas (navbar) untuk halaman yang sudah login
$judul_halaman = $judul_halaman ?? 'Mini Digital Library';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> | Mini Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-book-half me-2"></i>Mini Digital Library</a>
        <div class="ms-auto d-flex align-items-center gap-3 text-white">
            <span class="d-none d-sm-inline"><i class="bi bi-person-circle me-1"></i><?= e($_SESSION['nama'] ?? '') ?></span>
            <a href="logout.php" class="btn btn-sm btn-light"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </div>
</nav>
<main class="container py-4">
