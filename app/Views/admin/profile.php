<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Profile Admin</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Photo Card -->
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <?php 
                        $hasFoto = !empty($user['foto']) && file_exists(WRITEPATH . $user['foto']);
                        $userFoto = $hasFoto ? base_url('foto/user/' . basename($user['foto'])) : null;
                        
                        // Logika Inisial
                        $initials = '';
                        if (!$hasFoto) {
                            $nama = $user['nama'] ?? 'User';
                            $words = explode(' ', trim($nama));
                            $initials = strtoupper(substr($words[0], 0, 1));
                            if (count($words) > 1) {
                                $initials .= strtoupper(substr($words[1], 0, 1));
                            }
                        }
                        ?>

                        <?php if ($hasFoto): ?>
                            <img src="<?= $userFoto ?>" alt="Foto Profile" class="rounded-circle shadow mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle shadow bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px; font-size: 64px; font-weight: bold;">
                                <?= $initials ?>
                            </div>
                        <?php endif; ?>

                        <h4><?= esc($user['nama']) ?></h4>

                        <p class="text-muted">@<?= esc($user['username']) ?></p>
                        <p class="badge bg-primary">Administrator</p>
                        
                        <hr>
                        
                        <!-- Form Upload Foto -->
                        <form action="<?= base_url('admin/profile/update-foto') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <input type="file" name="foto" class="form-control" accept="image/*" required>
                                <small class="text-muted">Format: JPG, PNG, GIF, WebP (Max 2MB)</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-camera-fill"></i> Ganti Foto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Change Password Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i> Ganti Password</h5>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        
                        <form action="<?= base_url('admin/profile/update-password') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="password_lama" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password_baru" class="form-control" required minlength="8">
                                <small class="text-muted">Minimal 8 karakter, harus mengandung huruf dan angka</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="konfirmasi_password" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Simpan Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>