<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Manajemen Admin</h3>
        <p class="text-muted">Tambah, edit, dan hapus akun admin melalui Super Admin.</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Manajemen Admin</li>
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

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="mb-3 text-end">
      <a href="<?= base_url('superadmin/admins/create') ?>" class="btn btn-primary">+ Tambah Admin</a>
    </div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Dibuat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($admins)): ?>
                <?php foreach ($admins as $index => $admin): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($admin['username']) ?></td>
                    <td><?= esc($admin['nama']) ?></td>
                    <td><?= esc($admin['email'] ?? '-') ?></td>
                    <td><?= esc($admin['created_at'] ?? '-') ?></td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="<?= base_url('superadmin/admins/edit/' . $admin['id']) ?>" class="btn btn-sm btn-outline-info">Edit</a>
                        <a href="<?= base_url('superadmin/admins/delete-confirm/' . $admin['id']) ?>" class="btn btn-sm btn-outline-danger">Hapus</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4">Belum ada akun admin.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
