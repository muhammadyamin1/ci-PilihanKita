<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
  <script src="<?= base_url('js/sweetalert2.js') ?>"></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 400px;">
    <h4 class="mb-3 text-center">Login</h4>

    <?php if (session()->getFlashdata('error')): ?>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: '<?= session()->getFlashdata('error'); ?>'
        })
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
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</body>
</html>