<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Konfirmasi Hapus Admin</h3>
        <p class="text-muted">Pastikan Anda yakin sebelum menghapus admin beserta data yang terkait.</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/admins') ?>">Manajemen Admin</a></li>
          <li class="breadcrumb-item active" aria-current="page">Konfirmasi Hapus</li>
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
        <h5>Admin yang akan dihapus</h5>
        <ul>
          <li><strong>Username:</strong> <?= esc($admin['username']) ?></li>
          <li><strong>Nama:</strong> <?= esc($admin['nama']) ?></li>
          <li><strong>Email:</strong> <?= esc($admin['email'] ?? '-') ?></li>
        </ul>

        <div class="alert alert-warning">
          <strong>Perhatian:</strong> Menghapus admin dapat menyebabkan data kategori, calon, dan pemilih yang terkait ikut hilang karena relasi database.
        </div>

        <table class="table table-sm">
          <tbody>
            <tr>
              <td>Jumlah kategori</td>
              <td><strong><?= esc($kategoriCount) ?></strong></td>
            </tr>
            <tr>
              <td>Jumlah calon</td>
              <td><strong><?= esc($calonCount) ?></strong></td>
            </tr>
            <tr>
              <td>Jumlah pemilih</td>
              <td><strong><?= esc($pemilihCount) ?></strong></td>
            </tr>
            <tr>
              <td>Jumlah suara</td>
              <td><strong><?= esc($suaraCount) ?></strong></td>
            </tr>
          </tbody>
        </table>

        <form action="<?= base_url('admin/admins/delete/' . $admin['id']) ?>" method="post">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="confirm_delete" class="form-label">Ketik <strong>HAPUS ADMIN</strong> untuk konfirmasi</label>
            <input type="text" name="confirm_delete" id="confirm_delete" class="form-control" placeholder="HAPUS ADMIN" required>
          </div>

          <button type="submit" class="btn btn-danger">Hapus Admin</button>
          <a href="<?= base_url('admin/admins') ?>" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
