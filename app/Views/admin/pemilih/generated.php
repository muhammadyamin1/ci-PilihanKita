<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Pemilih Berhasil Di-Generate</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pemilih') ?>">Pemilih</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generated</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="app-content">

    <div class="container-fluid">
        <div class="alert alert-info">
            <strong>Perhatian:</strong> Password hanya ditampilkan satu kali di halaman ini. Pastikan untuk menyimpan atau mendownload CSV sebelum meninggalkan halaman ini.
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/pemilih/update-all-nama') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="batch_id" value="<?= $batch_id ?>">

            <div class="d-flex justify-content-between mb-3">
                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Kembali ke Daftar Pemilih</a>
                <div>
                    <a href="<?= base_url('admin/pemilih/download-csv/' . $batch_id) ?>" class="btn btn-success me-2">Download CSV</a>
                    <button type="submit" class="btn btn-primary">Update Semua Nama</button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Pemilih yang Di-Generate (<?= count($users) ?> user)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th width="5%">No</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= esc($u['username']); ?></td>
                                        <td class="text-monospace">
                                            <code><?= esc($plain_passwords[$u['id']] ?? 'N/A'); ?></code>
                                        </td>
                                        <td>
                                            <input type="text" name="nama[<?= $u['id'] ?>]" value="<?= esc($u['nama']); ?>" class="form-control form-control-sm" required>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
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