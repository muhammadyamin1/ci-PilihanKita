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
                            <input type="text" name="nama" class="form-control" required minlength="3" maxlength="100" autocomplete="off" placeholder="Nama lengkap">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email aktif" maxlength="100" autocomplete="off" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required minlength="4" maxlength="50" autocomplete="off" placeholder="Username">
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput" class="form-control" required minlength="8" maxlength="50" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" title="Minimal 8 karakter, huruf dan angka" autocomplete="off" placeholder="Minimal 8 karakter, huruf & angka">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Lihat/Sembunyikan Password">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <script>
                            document.getElementById('togglePassword').addEventListener('click', function() {
                                const passwordInput = document.getElementById('passwordInput');
                                const eyeIcon = document.getElementById('eyeIcon');
                                if (passwordInput.type === 'password') {
                                    passwordInput.type = 'text';
                                    eyeIcon.classList.remove('bi-eye');
                                    eyeIcon.classList.add('bi-eye-slash');
                                } else {
                                    passwordInput.type = 'password';
                                    eyeIcon.classList.remove('bi-eye-slash');
                                    eyeIcon.classList.add('bi-eye');
                                }
                            });
                        </script>

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