<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Pilihan Kita</title>
  <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
  <script src="<?= base_url('js/sweetalert2.js') ?>"></script>

  <style>
    .form-check-input:checked {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }

    a {
      text-decoration: none;
    }
  </style>

</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 400px;">
    <h4 class="mb-2 text-center" style="font-size: 36px;">🗳</h4>
    <h4 class="mb-0 text-center fw-bold text-primary"><a href="<?= base_url() ?>">Pilihan Kita</a></h4>
    <p class="text-center mb-3 text-muted">Masuk untuk melanjutkan</p>

    <?php if (session()->getFlashdata('error')): ?>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: '<?= session()->getFlashdata('error'); ?>'
        })
      </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: '<?= session()->getFlashdata('success'); ?>',
          timer: 2000,
          showConfirmButton: false
        });
      </script>
    <?php endif; ?>

    <form method="post" action="<?= base_url('login'); ?>">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-check form-switch mb-4 ms-3">
        <input class="form-check-input" type="checkbox" id="showPassword"
          onclick="document.querySelector('input[name=password]').type = this.checked ? 'text' : 'password'">
        <label class="form-check-label" for="showPassword">Tampilkan Password</label>
      </div>
      <button type="submit" class="btn btn-lg btn-primary w-100">Login</button>
    </form>
  </div>

  <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>