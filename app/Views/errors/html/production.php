<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Terjadi Kesalahan</title>
    <link
        rel="stylesheet"
        href="<?= base_url('css/bootstrap-icons.min.css') ?>"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.css') ?>" />
</head>

<body class="bg-light">

    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="text-center">

            <div class="mb-4">
                <h1 class="display-4 text-danger">Oops!</h1>
                <h4>Terjadi Kesalahan Sistem</h4>
            </div>

            <p class="text-muted">
                Sistem sedang mengalami gangguan. Silakan coba beberapa saat lagi.
            </p>

            <a href="<?= base_url() ?>" class="btn btn-primary mt-3">
                <i class="bi bi-house-door-fill me-1"></i>
                Kembali ke Beranda
            </a>

        </div>
    </div>

</body>

</html>