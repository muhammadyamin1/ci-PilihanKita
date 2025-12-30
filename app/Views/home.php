<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Kita | Modern Voting System</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="<?= base_url('css/bootstrap-icons.min.css') ?>"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <link href="<?= base_url('css/aos.css') ?>" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --accent-color: #ffee58;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        /* Navbar Styling */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 238, 88, 0.9) !important;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Hero Section */
        .hero-section {
            background: var(--primary-gradient);
            min-height: 90vh;
            display: flex;
            align-items: center;
            color: white;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0% 100%);
            padding-top: 73px;
            padding-bottom: 100px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
        }

        /* Card Styling (Glassmorphism) */
        .feature-card {
            border: none;
            border-radius: 20px;
            padding: 40px 30px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: #fdf2f2;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }

        /* Button Styling */
        .btn-custom {
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login {
            background: #2d3436;
            color: white;
            border: none;
        }

        .btn-login:hover {
            background: #000;
            color: white;
            transform: scale(1.05);
        }

        @keyframes pulse-custom {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 238, 88, 0.7);
            }

            50% {
                transform: scale(1.05);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(255, 238, 88, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 238, 88, 0);
            }
        }

        /* Selektor khusus tombol utama */
        .btn-lg {
            position: relative;
            z-index: 1;
            animation: pulse-custom 1.5s infinite;
        }

        /* Efek riak air tambahan menggunakan ::before */
        .btn-lg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50px;
            /* Samakan dengan border-radius tombol */
            border: 1px solid var(--accent-color);
            animation: ripple-out 1.5s infinite;
            z-index: -1;
        }

        @keyframes ripple-out {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        footer {
            background: #1a1a1a;
            color: rgba(255, 255, 255, 0.6);
        }

        #modalTentang .modal-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
        }

        #modalTentang .btn-outline-primary:hover,
        #modalTentang .btn-outline-dark:hover,
        #modalTentang .btn-outline-danger:hover {
            transform: translateY(-3px);
            transition: all 0.3s;
        }
    </style>
</head>

<body>

    <div class="modal fade" id="modalTentang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tentang Aplikasi & Pengembang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row align-items-center">
                    <div class="col-md-5 text-center border-end-md">
                        <div class="mb-3">
                            <img src="<?= base_url('assets/adminlte/img/Profil.jpg') ?>"
                                class="rounded-circle shadow-sm p-1 bg-white border"
                                style="width: 130px; height: 130px; object-fit: cover;" alt="Creator">
                        </div>
                        <h5 class="fw-bold mb-0">M. Yamin, S.Kom</h5>
                        <p class="text-primary small fw-semibold mb-3">Fullstack Developer</p>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <a href="https://linkedin.com/in/muhammadyamin1" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://github.com/muhammadyamin1" target="_blank" class="btn btn-outline-dark btn-sm rounded-circle">
                                <i class="bi bi-github"></i>
                            </a>
                            <a href="https://instagram.com/yamin1081" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle">
                                <i class="bi bi-instagram"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-7 ps-md-4">
                        <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Pilihan Kita v1.0</h6>
                        <p class="text-secondary small">
                            Sistem voting berbasis web yang dirancang untuk memastikan integritas data dengan kemudahan antarmuka pengguna.
                            Dibangun dengan CodeIgniter 4, sistem ini menerapkan prinsip integritas data melalui validasi server-side yang ketat dan manajemen basis data MariaDB yang teroptimasi.
                        </p>
                        
                        <h6 class="fw-bold mt-4 mb-2 small text-uppercase text-muted">Tech Stack yang Digunakan:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border"><i class="bi bi-code-slash text-danger"></i> CodeIgniter 4.6.3</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-database text-info"></i> MariaDB</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-bootstrap-fill text-primary"></i> Bootstrap 5</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-lightning-fill text-warning"></i> Axios</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-server text-secondary"></i> Apache/PHP 8.2</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <small class="text-muted">Dibuat dengan dedikasi untuk Demokrasi Digital Indonesia.</small>
            </div>
        </div>
    </div>
</div>

    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
                <span style="font-size: 1.5rem;">🗳️</span> Pilihan Kita
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold mx-2" href="#" data-bs-toggle="modal" data-bs-target="#modalTentang">Tentang Aplikasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-custom btn-login ms-lg-3" href="<?= base_url('login') ?>">Masuk Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="hero-title mb-4" data-aos="fade-down">
                        Demokrasi Digital <br> <span style="color: var(--accent-color)">Lebih Mudah & Transparan.</span>
                    </h1>
                    <p class="lead mb-5 opacity-75" data-aos="fade-up" data-aos-delay="200">
                        Media demokrasi sederhana untuk pemilihan <br>
                        Ketua Kelas, OSIS, Kating, atau BEM.
                    </p>
                    <div data-aos="zoom-in" data-aos-delay="400">
                        <a href="<?= base_url('login') ?>" class="btn btn-custom btn-lg shadow" style="background: var(--accent-color); color: #000;">
                            Mulai Berikan Suara
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="tentang" class="py-5 mt-n5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <div class="icon-box" style="background: #e3f2fd; color: #1976d2;">🔒</div>
                        <h4 class="fw-bold">Aman</h4>
                        <p class="text-muted">Setiap suara dihitung dengan adil dan transparan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card text-center">
                        <div class="icon-box" style="background: #fff9c4; color: #fbc02d;">⚡</div>
                        <h4 class="fw-bold">Cepat</h4>
                        <p class="text-muted">Voting bisa dilakukan hanya dengan beberapa klik.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card text-center">
                        <div class="icon-box" style="background: #e8f5e9; color: #388e3c;">📊</div>
                        <h4 class="fw-bold">Real-Time</h4>
                        <p class="text-muted">Perhitungan suara langsung bisa dilihat setelah voting.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 mt-5">
        <div class="container text-center">
            <p class="mb-2">© <?= date('Y') ?> <strong>Pilihan Kita</strong>. Build with ❤️ for Democracy.</p>
            <small>Platform Voting Digital No. 1 untuk Instansi Pendidikan</small>
        </div>
    </footer>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('js/aos.js') ?>"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-out-back'
        });

        // Script Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                nav.style.padding = '10px 0';
                nav.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            } else {
                nav.style.padding = '15px 0';
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>

</html>