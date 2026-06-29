<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0"><?= esc($title) ?></h3>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/admins') ?>">Manajemen Admin</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= esc($title) ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body">
        <form action="<?= base_url('admin/admins/' . $action) ?>" method="post">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= esc(old('nama', $admin['nama'] ?? '')) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= esc(old('username', $admin['username'] ?? '')) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= esc(old('email', $admin['email'] ?? '')) ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" <?= $admin ? '' : 'required' ?> placeholder="<?= $admin ? 'Kosongkan jika tidak ingin mengganti' : '' ?>">
            <?php if ($admin): ?>
              <small class="text-muted">Kosongkan jika password tidak ingin diubah.</small>
            <?php else: ?>
              <small class="text-muted">Minimal 8 karakter, huruf dan angka.</small>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" <?= $admin ? '' : 'required' ?> placeholder="<?= $admin ? 'Kosongkan jika tidak ingin mengganti' : '' ?>">
          </div>

          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="<?= base_url('admin/admins') ?>" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
