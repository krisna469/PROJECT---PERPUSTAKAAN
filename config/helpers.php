<?php
/**
 * Fungsi bantu: auth, flash message, CSRF, logika status, upload file.
 */

const UPLOAD_DIR   = __DIR__ . '/../uploads/';
const MAX_FILE     = 2 * 1024 * 1024; // 2 MB
const KATEGORI     = ['Teknologi', 'Sains', 'Novel', 'Sejarah', 'Bisnis', 'Pendidikan', 'Lainnya'];

/** Escape output HTML */
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/** Redirect + stop */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Proteksi halaman: hanya user yang sudah login */
function wajib_login(): void
{
    if (empty($_SESSION['user_id'])) {
        $_SESSION['flash'] = ['danger', 'Silakan login terlebih dahulu.'];
        redirect('index.php');
    }
}

/** Flash message (sekali tampil) */
function set_flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = [$tipe, $pesan];
}

function tampil_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        [$tipe, $pesan] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($tipe) . ' alert-dismissible fade show shadow-sm" role="alert">'
           . e($pesan)
           . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

/** CSRF token */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function cek_csrf(): void
{
    $kirim = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $kirim)) {
        http_response_code(403);
        die('Token keamanan tidak valid.');
    }
}

/**
 * Logika bisnis: tentukan label status ketersediaan buku (percabangan).
 * Mengembalikan array [label, warna badge Bootstrap].
 */
function label_status(int $stok): array
{
    if ($stok > 5) {
        return ['Tersedia', 'success'];
    } elseif ($stok > 0) {
        return ['Tersedia (terbatas)', 'warning'];
    } else {
        return ['Dipinjam', 'danger'];
    }
}

/**
 * Proses upload sampul buku.
 * @return array [nama_file|null, pesan_error|null]
 */
function proses_upload_sampul(array $file, bool $wajib): array
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $wajib ? [null, 'Sampul buku wajib diupload.'] : [null, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload gagal (kode error ' . $file['error'] . ').'];
    }
    if ($file['size'] > MAX_FILE) {
        return [null, 'Ukuran file maksimal 2MB.'];
    }

    // Validasi ekstensi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $ext_boleh = ['jpg', 'jpeg', 'png'];
    if (!in_array($ext, $ext_boleh, true)) {
        return [null, 'Format file harus .jpg atau .png.'];
    }

    // Validasi MIME asli & pastikan benar-benar gambar
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    switch ($mime) {
        case 'image/jpeg':
        case 'image/png':
            break;
        default:
            return [null, 'Isi file bukan gambar JPG/PNG yang valid.'];
    }
    if (@getimagesize($file['tmp_name']) === false) {
        return [null, 'File gambar rusak atau tidak valid.'];
    }

    $nama_baru = 'sampul_' . bin2hex(random_bytes(8)) . '.' . ($mime === 'image/png' ? 'png' : 'jpg');
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $nama_baru)) {
        return [null, 'Gagal menyimpan file ke server.'];
    }
    return [$nama_baru, null];
}

/** Hapus file fisik sampul dari folder uploads */
function hapus_file_sampul(?string $nama): void
{
    if ($nama) {
        $path = UPLOAD_DIR . basename($nama);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

/** Validasi input teks buku. Mengembalikan daftar error. */
function validasi_buku(array $d): array
{
    $err = [];
    if (trim($d['judul']) === '' || mb_strlen($d['judul']) > 150) {
        $err[] = 'Judul wajib diisi (maks. 150 karakter).';
    }
    if (!in_array($d['kategori'], KATEGORI, true)) {
        $err[] = 'Kategori tidak valid.';
    }
    if (trim($d['penulis']) === '' || mb_strlen($d['penulis']) > 100) {
        $err[] = 'Penulis wajib diisi (maks. 100 karakter).';
    }
    if (!ctype_digit($d['tahun']) || (int) $d['tahun'] < 1900 || (int) $d['tahun'] > (int) date('Y')) {
        $err[] = 'Tahun terbit harus angka antara 1900 dan ' . date('Y') . '.';
    }
    if (!ctype_digit($d['stok']) || (int) $d['stok'] > 9999) {
        $err[] = 'Stok harus berupa angka 0 atau lebih.';
    }
    return $err;
}
