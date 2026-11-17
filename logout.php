<?php
session_start();

// Hapus seluruh data session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
// Hapus cookie remember me
setcookie('remember_username', '', time() - 3600, '/');
session_destroy();

// Mulai session baru hanya untuk pesan flash
session_start();
$_SESSION['flash'] = ['success', 'Anda berhasil logout.'];
header('Location: index.php');
exit;
