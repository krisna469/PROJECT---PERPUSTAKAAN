<?php
require_once 'config/koneksi.php';
wajib_login();
$judul_halaman = 'Dashboard';

// Pencarian & filter kategori (GET)
$cari     = trim($_GET['cari'] ?? '');
$kategori = $_GET['kategori'] ?? '';

$sql    = 'SELECT * FROM buku WHERE 1=1';
$tipe   = '';
$param  = [];
if ($cari !== '') {
    $sql .= ' AND (judul LIKE ? OR penulis LIKE ?)';
    $tipe .= 'ss';
    $param[] = "%$cari%";
    $param[] = "%$cari%";
}
if (in_array($kategori, KATEGORI, true)) {
    $sql .= ' AND kategori = ?';
    $tipe .= 's';
    $param[] = $kategori;
}
$sql .= ' ORDER BY id DESC';

$stmt = $koneksi->prepare($sql);
if ($param) {
    $stmt->bind_param($tipe, ...$param);
}
$stmt->execute();
$hasil = $stmt->get_result();
$daftar = $hasil->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Statistik ringkas
$total    = (int) $koneksi->query('SELECT COUNT(*) c FROM buku')->fetch_assoc()['c'];
$tersedia = (int) $koneksi->query('SELECT COUNT(*) c FROM buku WHERE stok > 0')->fetch_assoc()['c'];
$dipinjam = $total - $tersedia;

require 'config/header.php';
?>
<?php tampil_flash(); ?>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card stat-blue"><i class="bi bi-collection"></i><div><div class="stat-num"><?= $total ?></div><div>Total Buku</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-green"><i class="bi bi-check-circle"></i><div><div class="stat-num"><?= $tersedia ?></div><div>Tersedia</div></div></div></div>
    <div class="col-md-4"><div class="stat-card stat-red"><i class="bi bi-arrow-left-right"></i><div><div class="stat-num"><?= $dipinjam ?></div><div>Dipinjam</div></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-journal-bookmark me-2"></i>Daftar Buku</h5>
            <a href="tambah_buku.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Buku</a>
        </div>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="cari" class="form-control" placeholder="Cari judul / penulis..." value="<?= e($cari) ?>">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach (KATEGORI as $k): ?>
                        <option value="<?= e($k) ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th><th>Sampul</th><th>Judul</th><th>Kategori</th><th>Penulis</th>
                        <th>Tahun</th><th>Stok</th><th>Status</th><th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="9" class="text-center text-muted py-5">Belum ada data buku.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $i => $b):
                    [$label, $warna] = label_status((int) $b['stok']);
                    $gambar = is_file(UPLOAD_DIR . $b['sampul']) ? 'uploads/' . $b['sampul'] : 'assets/img/no-cover.png';
                ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><img src="<?= e($gambar) ?>" alt="Sampul <?= e($b['judul']) ?>" class="cover-thumb"
                                 data-bs-toggle="modal" data-bs-target="#modalGambar" data-src="<?= e($gambar) ?>"></td>
                        <td class="fw-semibold"><?= e($b['judul']) ?></td>
                        <td><span class="badge bg-secondary-subtle text-secondary-emphasis"><?= e($b['kategori']) ?></span></td>
                        <td><?= e($b['penulis']) ?></td>
                        <td><?= e($b['tahun_terbit']) ?></td>
                        <td><?= (int) $b['stok'] ?></td>
                        <td><span class="badge bg-<?= $warna ?>"><?= e($label) ?></span></td>
                        <td class="text-center text-nowrap">
                            <a href="edit_buku.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                            <form action="hapus_buku.php" method="POST" class="d-inline form-hapus">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal preview sampul -->
<div class="modal fade" id="modalGambar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0"><img id="gambarBesar" src="" class="img-fluid rounded" alt="Sampul"></div>
    </div>
</div>
<?php require 'config/footer.php'; ?>
