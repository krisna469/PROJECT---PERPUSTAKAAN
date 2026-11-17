<?php
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
cek_csrf();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if ($username === '' || $password === '') {
    set_flash('danger', 'Username dan password wajib diisi.');
    redirect('index.php');
}

$stmt = $koneksi->prepare('SELECT id, username, nama_lengkap, password FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama']     = $user['nama_lengkap'];

    // Cookie "Remember Me": simpan username saja (30 hari)
    if ($remember) {
        setcookie('remember_username', $user['username'], [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        setcookie('remember_username', '', time() - 3600, '/');
    }

    set_flash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
    redirect('dashboard.php');
}

set_flash('danger', 'Username atau password salah.');
redirect('index.php');
