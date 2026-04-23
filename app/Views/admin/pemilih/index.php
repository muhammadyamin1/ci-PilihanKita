<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Pemilih</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manajemen</li>
                    <li class="breadcrumb-item active" aria-current="page">Pemilih</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="app-content">

    <div class="container-fluid">
        <div class="text-end mb-2">

            <div class="btn-group">
                <a href="<?= base_url('admin/pemilih/tambah') ?>" class="btn btn-primary btn-sm">
                    + Tambah Manual
                </a>
                <a href="<?= base_url('admin/pemilih/generate') ?>" class="btn btn-success btn-sm">
                    Generate Otomatis
                </a>
                <a href="<?= base_url('admin/pemilih/import') ?>" class="btn btn-warning btn-sm">
                    Impor Excel
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <?php
                $error   = session()->getFlashdata('error');
                $success = session()->getFlashdata('success');
                ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                <?php elseif ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <script>
                    setTimeout(function() {
                        var alerts = document.querySelectorAll('.alert');
                        alerts.forEach(function(alert) {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        });
                    }, 10000);
                </script>
                <!-- FILTER + TOTAL -->
                <div class="d-flex justify-content-between align-items-center p-3">

                    <form method="get" class="d-flex align-items-center">
                        <label class="me-2 mb-0">Tampilkan:</label>
                        <select name="perPage"
                            class="form-select form-select-sm"
                            style="width:90px;"
                            onchange="this.form.submit()">
                            <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </form>

                    <div>
                        Total: <strong><?= $total; ?></strong> Pemilih
                    </div>

                </div>
            </div>
            <div class="table-responsive px-3">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kategori</th>
                            <th>Generated</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)) : ?>

                            <?php
                            $currentPage = $pager->getCurrentPage();
                            $no = 1 + ($perPage * ($currentPage - 1));
                            ?>

                            <?php foreach ($users as $u) : ?>
                                <tr class="align-middle">
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($u['username']); ?></td>
                                    <td><?= esc($u['nama']); ?></td>
                                    <td><?= $u['email'] ?? '-'; ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($u['nama_kategori'])): ?>
                                            <span class="badge bg-success">
                                                <?= esc($u['nama_kategori']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                Kategori Belum Ditentukan
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($u['generated']) : ?>
                                            <span class="badge bg-success">Ya</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary">Manual</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($u['sudah_memilih']) : ?>
                                            <span class="badge bg-primary">Sudah Memilih</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark">Belum</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center" width="20%">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('admin/pemilih/edit/' . $u['id']) ?>"
                                                class="btn btn-sm btn-outline-info"
                                                data-bs-toggle="tooltip"
                                                title="Edit Data Pemilih">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="<?= base_url('admin/pemilih/reset/' . $u['id']) ?>"
                                                class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="tooltip"
                                                title="Reset Password">
                                                <i class="bi bi-arrow-clockwise"></i> Reset
                                            </a>
                                            <form action="<?= base_url('admin/pemilih/hapus/' . $u['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Hapus Pemilih">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    Belum ada data pemilih
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="pb-0 pt-2 ps-3">
                <nav>
                    <ul class="pagination pagination-sm">
                        <?= $pager->links('default', 'bootstrap') ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Auto-hide alerts after 10 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 10000);
</script>

<?= $this->endSection(); ?>