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
<div class="app-content">
    <div class="container-fluid">
        <div class="col-12">
            <div id="status-alert">
                <?php $aktif = array_filter($kategori, fn($k) => $k['aktif'] == 1); ?>
                <?php if ($aktif): ?>
                    <div class="alert alert-success">
                        Voting aktif di kategori: <strong><?= esc($aktif[array_key_first($aktif)]['nama']) ?></strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Belum ada kategori aktif. Voting belum dimulai.
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
                <button type="submit" id="btnSubmit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Konfigurasi global axios - sudah define di bawah
    const BASE_URL = '<?= base_url() ?>';

    // Sanitasi string untuk mencegah XSS saat ditampilkan via innerHTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showFlash(message, type = 'success') {
        // Sanitasi message untuk mencegah XSS
        const safeMessage = escapeHtml(message);
        const alert = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${safeMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;

        const flash = document.getElementById('flash-message');
        flash.innerHTML = alert;

        // Durasi tampil: 5 detik kalau success, 10 detik kalau selain itu
        const duration = (type === 'success') ? 5000 : 15000;

        setTimeout(() => {
            flash.innerHTML = '';
        }, duration);
    }

    // Ambil CSRF token dari meta atau cookie
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // Helper untuk POST dengan axios + CSRF
    function axiosPostCsrf(url, data = {}) {
        return axios.post(url, data, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
    }

    function toggleActive(id, event) {
        const clickedCheckbox = event.target;
        const wasChecked = clickedCheckbox.checked;

        clickedCheckbox.disabled = true;

        axiosPostCsrf(`/admin/kategori/toggle/${id}`)
            .then(res => {
                // Sanitasi message dari server
                const message = res.data.message || '';
                showFlash(message);

                if (message.includes('diaktifkan')) {
                    // Kalau kategori diaktifkan → matikan semua kecuali yang diklik
                    document.querySelectorAll('.switch input[type="checkbox"]').forEach(cb => cb.checked = false);
                    clickedCheckbox.checked = true;

                    // Ubah alert ke kategori aktif (textContent aman dari XSS)
                    const namaKategori = clickedCheckbox.closest('tr').querySelector('td:nth-child(2)').textContent;
                    document.getElementById('status-alert').innerHTML =
                        `<div class="alert alert-success">
                        Voting aktif di kategori: <strong>${escapeHtml(namaKategori)}</strong>
                    </div>`;
                } else {
                    // Kalau dinonaktifkan → biarkan semua off
                    clickedCheckbox.checked = false;

                    document.getElementById('status-alert').innerHTML =
                        `<div class="alert alert-warning">
                        Belum ada kategori aktif. Voting belum dimulai.
                    </div>`;
                }
            })
            .catch(e => {
                let pesanError = 'Gagal mengubah status kategori.';

                // Ambil pesan error dari response server jika ada
                if (e.response && e.response.data && e.response.data.message) {
                    pesanError = e.response.data.message;
                }

                showFlash(pesanError, 'danger');
                clickedCheckbox.checked = wasChecked;
            })
            .finally(() => {
                clickedCheckbox.disabled = false;
            });
    }

    function hapusKategori(id, nama) {
        if (!confirm(`Yakin ingin menghapus kategori "${nama}"?`)) return;

        axiosPostCsrf(`/admin/kategori/delete/${id}`)
            .then(res => {
                if (res.data.success) {
                    showFlash(res.data.message || 'Kategori berhasil dihapus.');

                    const row = document.getElementById(`row-${id}`);
                    if (!row) return;

                    const checkbox = row.querySelector('.switch input[type="checkbox"]');

                    // Jika kategori yang dihapus sedang aktif
                    if (checkbox && checkbox.checked) {
                        document.getElementById('status-alert').innerHTML = `
                        <div class="alert alert-warning">
                            Belum ada kategori aktif. Voting belum dimulai.
                        </div>`;
                    }

                    // Hapus baris dari tabel tanpa reload
                    row.remove();
                } else {
                    showFlash(res.data.message || 'Gagal menghapus kategori.', 'danger');
                }
            })
            .catch(error => {
                let msg = 'Gagal menghapus kategori.';

                // Ambil pesan error dari response server jika ada
                if (error.response && error.response.data && error.response.data.message) {
                    msg = error.response.data.message;
                }

                showFlash(msg, 'danger');
            });
    }
</script>
<script src="<?= base_url('js/axios.min.js') ?>"></script>
<script>
    // Konfigurasi global axios setelah script dimuat
    axios.defaults.baseURL = BASE_URL;
</script>
<script>
    function submitTambahKategori() {
        const form = document.getElementById('formTambah');
        const btnSubmit = document.getElementById('btnSubmit');
        const inputNama = form.querySelector('input[name="nama"]');
        const nama = inputNama.value.trim();

        // Validasi frontend: nama tidak boleh kosong
        if (!nama) {
            showFlash('Nama kategori wajib diisi.', 'danger');
            inputNama.focus();
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Menyimpan...';

        const formData = new FormData(form);
        axiosPostCsrf('/admin/kategori/store', formData)
            .then(res => {
                if (res.data.success) {
                    showFlash(res.data.message);

                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambah'));
                    modal.hide();

                    // Tambahkan baris baru ke tabel
                    const tbody = document.querySelector('table tbody');
                    const newRow = document.createElement('tr');
                    const newId = res.data.newId ?? 'baru';
                    const rowCount = tbody.querySelectorAll('tr').length + 1;
                    const safeNama = escapeHtml(nama);

                    newRow.id = `row-${newId}`;
                    newRow.innerHTML = `
                        <td>${rowCount}</td>
                        <td>${safeNama}</td>
                        <td>
                        <label class="switch">
                            <input type="checkbox" onchange="toggleActive(${newId}, event)">
                            <span class="slider"></span>
                        </label>
                        </td>
                        <td>
                        <button class="btn btn-danger btn-sm" onclick="hapusKategori(${newId}, '${safeNama.replace(/'/g, "\\'")}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        </td>
                    `;
                    tbody.appendChild(newRow);

                    form.reset();
                } else {
                    showFlash(res.data.message || 'Gagal menambah kategori.', 'danger');
                }
            })
            .catch(error => {
                let msg = 'Gagal menambah kategori.';
                if (error.response && error.response.data && error.response.data.message) {
                    msg = error.response.data.message;
                }
                showFlash(msg, 'danger');
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Simpan';
            });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tangkap modal
        const modalTambahEl = document.getElementById('modalTambah');

        // Event saat modal benar-benar ditampilkan
        modalTambahEl.addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                const inputNama = modalTambahEl.querySelector('input[name="nama"]');
                if (inputNama) inputNama.focus();
            }, 100);
        });

        // Handler submit form tetap
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            submitTambahKategori();
        });
    });

    // (Script handler submit sudah di atas - submitTambahKategori)
</script>

<?= $this->endSection(); ?>