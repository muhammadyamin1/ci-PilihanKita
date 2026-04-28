<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Kode | Pilihan Kita</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('js/sweetalert2.js') ?>"></script>

    <style>
        a {
            text-decoration: none;
        }
        
        .verification-input {
            letter-spacing: 10px;
            font-size: 24px;
            text-align: center;
        }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px;">
        <h4 class="text-center fw-bold text-primary mb-2">Verifikasi Kode</h4>
        <p class="text-center text-muted mb-4">Masukkan kode verifikasi yang dikirim ke email Anda</p>

        <?php if (session()->getFlashdata('error')): ?>
            <script>
                Swal.fire('Oops', '<?= session()->getFlashdata('error') ?>', 'error');
            </script>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '<?= session()->getFlashdata('success') ?>'
                });
            </script>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label class="form-label">Kode Verifikasi</label>
                <input type="text" name="kode" class="form-control verification-input" 
                       maxlength="6" pattern="[0-9]*" inputmode="numeric" 
                       required autofocus placeholder="000000">
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required minlength="8">
                <small class="text-muted">Minimal 8 karakter, harus mengandung huruf dan angka</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Reset Password</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= base_url('forgot-password') ?>" class="text-muted">← Kembali</a>
        </div>
    </div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        // Auto-format input hanya angka
        document.querySelector('input[name="kode"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>