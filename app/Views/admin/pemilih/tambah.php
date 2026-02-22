<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mt-1">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tambah User Pemilih</h5>
                </div>
                <div class="card-body">

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

                    <form action="<?= base_url('admin/pemilih/simpan') ?>" method="post">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori Pemilihan</label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id']; ?>">
                                        <?= esc($k['nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">

                            <!-- Kiri -->
                            <a href="<?= base_url('admin/pemilih') ?>"
                                class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>

                            <!-- Kanan -->
                            <div>
                                <button type="submit" name="action" value="save"
                                    class="btn btn-primary btn-sm me-1">
                                    <i class="bi bi-save"></i> Simpan
                                </button>

                                <button type="submit" name="action" value="save_add"
                                    class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle"></i> Simpan & Tambah Lagi
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>