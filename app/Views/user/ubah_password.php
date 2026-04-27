<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Ubah Password</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (session()->getFlashdata('warning')): ?>
                    <div class="alert alert-warning"><?= session()->getFlashdata('warning') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <strong>Password Anda adalah password default sistem.</strong><br>
                    Silakan ubah password Anda terlebih dahulu sebelum melanjutkan untuk memilih.
                </div>

                <form action="<?= base_url('user/ubah-password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required minlength="8">
                        <small class="text-muted">Minimal 8 karakter, harus mengandung huruf dan angka</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="konfirmasi_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>