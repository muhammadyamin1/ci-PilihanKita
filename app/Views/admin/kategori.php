<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(22px);
    }
</style>
<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Kategori Pemilihan</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Kelola Kategori Pemilihan</h5>
        </div>
        <div class="card-body">
            <div id="flash-message"></div>

            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                + Tambah Kategori
            </button>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori as $i => $row): ?>
                        <tr id="row-<?= $row['id'] ?>">
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nama']) ?></td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" <?= $row['aktif'] ? 'checked' : '' ?>
                                        onchange="toggleActive(<?= $row['id'] ?>, event)">
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="hapusKategori(<?= $row['id'] ?>, '<?= esc($row['nama']) ?>')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form id="formTambah" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nama" class="form-control" placeholder="Nama kategori..." required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('js/axios.min.js') ?>"></script>
<script>
    function showFlash(message, type = 'success') {
        const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
        document.getElementById('flash-message').innerHTML = alert;
        setTimeout(() => document.getElementById('flash-message').innerHTML = '', 5000);
    }

    function toggleActive(id, event) {
        const clickedCheckbox = event.target; // simpan referensi switch yang diklik
        const wasChecked = clickedCheckbox.checked; // simpan status awalnya

        // Matikan interaksi selama request biar gak double klik
        clickedCheckbox.disabled = true;

        axios.post(`/admin/kategori/toggle/${id}`)
            .then(res => {
                showFlash(res.data.message);

                // Jika server sukses, update tampilan:
                if (res.data.success) {
                    // Matikan semua checkbox lain
                    document.querySelectorAll('.switch input[type="checkbox"]').forEach(cb => {
                        cb.checked = false;
                    });
                    // Aktifkan kembali yang diklik
                    clickedCheckbox.checked = true;
                }
            })
            .catch(() => {
                showFlash('Gagal mengubah status kategori', 'danger');
                // Balikkan ke status semula kalau gagal
                clickedCheckbox.checked = wasChecked;
            })
            .finally(() => {
                // Aktifkan kembali interaksi
                clickedCheckbox.disabled = false;
            });
    }

    function hapusKategori(id, nama) {
        if (!confirm(`Yakin ingin menghapus kategori "${nama}"?`)) return;

        axios.post(`/admin/kategori/delete/${id}`)
            .then(res => {
                if (res.data.success) {
                    showFlash(res.data.message);
                    // Hapus baris dari tabel tanpa reload
                    document.getElementById(`row-${id}`).remove();
                } else {
                    showFlash(res.data.message, 'danger');
                }
            })
            .catch(() => showFlash('Gagal menghapus kategori', 'danger'));
    }

    document.getElementById('formTambah').addEventListener('submit', function(e) {
        e.preventDefault();
        axios.post('/admin/kategori/store', new FormData(this))
            .then(res => {
                if (res.data.success) {
                    showFlash(res.data.message);

                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambah'));
                    modal.hide();

                    // Tambahkan baris baru ke tabel
                    const tbody = document.querySelector('table tbody');
                    const newRow = document.createElement('tr');
                    const newId = res.data.newId ?? 'baru'; // kalau controller kirim ID baru
                    const rowCount = tbody.querySelectorAll('tr').length + 1;

                    newRow.id = `row-${newId}`;
                    newRow.innerHTML = `
                        <td>${rowCount}</td>
                        <td>${document.querySelector('[name="nama"]').value}</td>
                        <td>
                        <label class="switch">
                            <input type="checkbox" onchange="toggleActive(${newId}, event)">
                            <span class="slider"></span>
                        </label>
                        </td>
                        <td>
                        <button class="btn btn-danger btn-sm" onclick="hapusKategori(${newId}, '${document.querySelector('[name="nama"]').value}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        </td>
                    `;
                    tbody.appendChild(newRow);

                    // Kosongkan form input
                    document.getElementById('formTambah').reset();
                }
            })
            .catch(() => showFlash('Gagal menambah kategori', 'danger'));
    });
</script>

<?= $this->endSection(); ?>