<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Konfirmasi Hapus Pemilih</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pemilih') ?>">Pemilih</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hapus</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="app-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php $error = session()->getFlashdata('error'); ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= esc($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <h5 class="card-title" style="float:none; margin-bottom:0.5rem;">Anda akan menghapus pemilih</h5>

                        <p class="text-muted">
                            Username: <strong><?= esc($user['username']) ?></strong><br>
                            Nama: <strong><?= esc($user['nama']) ?></strong>
                        </p>

                        <p class="text-warning">User tidak dapat dikembalikan setelah dihapus.</p>

                        <form action="<?= base_url('admin/pemilih/hapus/' . $user['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="confirm_delete" class="form-label">Ketik <strong>HAPUS</strong> untuk mengonfirmasi</label>
                                <input type="text" name="confirm_delete" id="confirm_delete" class="form-control" placeholder="HAPUS" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>