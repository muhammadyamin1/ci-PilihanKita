<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Kita</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="<?= base_url('css/aos.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #ffee58;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">🗳 Pilihan Kita</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Aplikasi</a></li>
                    <li class="nav-item">
                        <a class="btn btn-dark ms-2" href="<?= base_url('login') ?>">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-light text-center py-5">
        <div class="container">
            <h1 class="fw-bold mb-3" data-aos="fade-up">Selamat Datang di Aplikasi Voting <br> <span class="text-primary">Pilihan Kita</span></h1>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                Media demokrasi sederhana untuk pemilihan Ketua Kelas, OSIS, Kating, atau BEM.
            </p>
            <a href="<?= base_url('login') ?>" class="btn btn-primary mt-3" data-aos="zoom-in" data-aos-delay="400">Mulai Login</a>
        </div>
    </header>

    <!-- Tentang Aplikasi -->
    <section id="tentang" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4" data-aos="zoom-in">Tentang Aplikasi</h2>
            <div class="row">
                <div class="col-md-4 mb-3" data-aos="fade-right">
                    <div class="card shadow-sm p-3 text-center">
                        <h5>🔒 Aman</h5>
                        <p>Setiap suara dihitung dengan adil dan transparan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3" data-aos="fade-up">
                    <div class="card shadow-sm p-3 text-center">
                        <h5>⚡ Cepat</h5>
                        <p>Voting bisa dilakukan hanya dengan beberapa klik.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3" data-aos="fade-left">
                    <div class="card shadow-sm p-3 text-center">
                        <h5>📊 Hasil Real-time</h5>
                        <p>Perhitungan suara langsung bisa dilihat setelah voting.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-3 bg-dark text-white">
        <p class="mb-0">&copy; <?= date('Y') ?> Pilihan Kita. Made With 💖 | All Rights Reserved.</p>
    </footer>

    <!-- Scripts -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('js/aos.js') ?>"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>
