document.addEventListener('DOMContentLoaded', function () {
    // Tampil/sembunyi password
    var tombol = document.getElementById('togglePw');
    if (tombol) {
        tombol.addEventListener('click', function () {
            var pw = document.getElementById('password');
            var icon = tombol.querySelector('i');
            var tampil = pw.type === 'password';
            pw.type = tampil ? 'text' : 'password';
            icon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    // Preview sampul + validasi sisi klien (tipe & ukuran)
    var input = document.getElementById('inputSampul');
    if (input) {
        input.addEventListener('change', function () {
            var f = input.files[0];
            if (!f) return;
            if (['image/jpeg', 'image/png'].indexOf(f.type) === -1) {
                alert('Format file harus JPG atau PNG.'); input.value = ''; return;
            }
            if (f.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB.'); input.value = ''; return;
            }
            document.getElementById('previewSampul').src = URL.createObjectURL(f);
        });
    }

    // Konfirmasi hapus
    document.querySelectorAll('.form-hapus').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!confirm('Yakin ingin menghapus buku ini beserta gambar sampulnya?')) ev.preventDefault();
        });
    });

    // Modal gambar besar
    var modal = document.getElementById('modalGambar');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (ev) {
            document.getElementById('gambarBesar').src = ev.relatedTarget.getAttribute('data-src');
        });
    }
});
