<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Reset Password</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pemilih') ?>">Pemilih</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reset Password</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="app-content">

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0 text-dark"><i class="bi bi-exclamation-triangle me-2"></i>Reset Password</h5>
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

                        <div class="alert alert-warning">
                            <strong>Informasi User:</strong><br>
                            Username: <strong><?= esc($user['username']) ?></strong><br>
                            Nama: <strong><?= esc($user['nama']) ?></strong>
                        </div>

                        <form action="<?= base_url('admin/pemilih/reset/' . $user['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="alasan_pilihan" class="form-label">
                                    <span class="text-danger">*</span> Alasan Reset Password
                                </label>
                                <select name="alasan_pilihan" id="alasan_pilihan" class="form-select" onchange="toggleLainnya()" required>
                                    <option value="">Pilih Alasan</option>
                                    <option value="User lupa password">User lupa password</option>
                                    <option value="Password expired">Password expired</option>
                                    <option value="User minta reset">User minta reset</option>
                                    <option value="Akun terkunci">Akun terkunci</option>
                                    <option value="lainnya">Lainnya...</option>
                                </select>
                            </div>

                            <div class="mb-3" id="alasan_lainnya_group" style="display: none;">
                                <label for="alasan_lain" class="form-label">
                                    <span class="text-danger">*</span> Spesifikasi Alasan
                                </label>
                                <textarea name="alasan" id="alasan_lain" class="form-control" rows="3" placeholder="Jelaskan alasan lainnya..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin mereset password user ini?')">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLainnya() {
    var select = document.getElementById('alasan_pilihan');
    var lainnyaGroup = document.getElementById('alasan_lainnya_group');
    var alasanLain = document.getElementById('alasan_lain');
    
    if (select.value === 'lainnya') {
        lainnyaGroup.style.display = 'block';
        alasanLain.setAttribute('required', 'required');
    } else {
        lainnyaGroup.style.display = 'none';
        alasanLain.removeAttribute('required');
        alasanLain.value = '';
    }
}

// Override form submit untuk gabungkan alasan
document.querySelector('form').addEventListener('submit', function(e) {
    var select = document.getElementById('alasan_pilihan');
    var alasanLain = document.getElementById('alasan_lain');
    
    if (select.value === 'lainnya') {
        // Gunakan nilai dari textarea
    } else if (select.value !== '') {
        // Set nilai textarea dengan pilihan
        alasanLain.value = select.value;
    }
});
</script>

<?= $this->endSection(); ?>