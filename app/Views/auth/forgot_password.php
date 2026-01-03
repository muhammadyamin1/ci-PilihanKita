<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lupa Password | Pilihan Kita</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('js/sweetalert2.js') ?>"></script>

    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px;">
        <h4 class="text-center fw-bold text-primary mb-2">Reset Password</h4>
        <p class="text-center text-muted mb-4">Masukkan username Anda</p>

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
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>

            <button class="btn btn-primary w-100">Kirim Permintaan</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= base_url('login') ?>" class="text-muted">← Kembali ke Login</a>
        </div>
    </div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>