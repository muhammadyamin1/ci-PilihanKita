<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Hasil Import Pemilih</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <strong>Password telah dibuat otomatis untuk pemilih yang kosong password-nya.</strong><br>
                    Silakan simpan atau download CSV sebelum meninggalkan halaman ini.
                </div>

                <div class="mb-3">
                    <a href="<?= base_url('admin/pemilih/download-import-csv/' . $batch_id) ?>" class="btn btn-success">
                        <i class="bi bi-download"></i> Download CSV Semua Data + Password
                    </a>
                    <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Kembali ke Daftar Pemilih</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($user['username']) ?></td>
                                    <td><?= esc($user['nama']) ?></td>
                                    <td><?= esc($user['email'] ?? '-') ?></td>
                                    <td>
                                        <strong class="text-danger"><?= esc($plain_passwords[$user['id']] ?? 'N/A') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>