<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Cleanup Database</h3>
        <p class="text-muted">Super Admin dapat melihat data bermasalah terlebih dahulu sebelum menghapus.</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Cleanup</li>
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

    <?php $cleanupResult = session()->getFlashdata('cleanup_result'); ?>
    <?php $cleanupProgress = session()->getFlashdata('cleanup_progress'); ?>
    <?php $cleanupOpenStep = session()->getFlashdata('cleanup_open_step'); ?>
    <?php if (empty($cleanupAllowedStep)) {
        $cleanupOpenStep = '';
    } elseif (empty($cleanupOpenStep)) {
        $cleanupOpenStep = $cleanupAllowedStep;
    }
?>

    <?php if (!empty($cleanupResult)): ?>
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title" style="float:none; margin-bottom:0.5rem;">Hasil Hapus File Orphan</h5>
          <?php foreach ($cleanupResult as $type => $result): ?>
            <div class="mb-2">
              <strong><?= ucfirst($type) ?></strong>
              <?php if ($result['status'] === 'success'): ?>
                <div>Total file dipindai: <?= $result['total_files_scanned'] ?></div>
                <div>File dihapus: <?= $result['deleted_count'] ?></div>
                <div>Dipertahankan: <?= $result['kept_count'] ?></div>
                <?php if (!empty($result['deleted_files'])): ?>
                  <div>File yang dihapus: <code><?= esc(implode(', ', $result['deleted_files'])) ?></code></div>
                <?php endif; ?>
              <?php else: ?>
                <div class="text-danger"><?= esc($result['message']) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($cleanupProgress)): ?>
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title mt-2" style="float:none; margin-bottom:0.5rem;">Progress Hapus Data</h5>
          <ul class="mb-2">
            <?php foreach ($cleanupProgress as $step): ?>
              <li><?= esc($step) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Preview File Orphan</h5>
          </div>
          <div class="card-body">
            <p class="text-muted">File yang ada di folder upload tetapi tidak ditemukan di database.</p>

            <h6>User (Admin Profile)</h6>
            <div>Total file orphan: <strong><?= count($preview['user']['files']) ?></strong></div>
            <?php if (!empty($preview['user']['files'])): ?>
              <ul class="small">
                <?php foreach ($preview['user']['files'] as $file): ?>
                  <li><?= esc($file) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <h6 class="mt-3">Calon</h6>
            <div>Total file orphan: <strong><?= count($preview['calon']['files']) ?></strong></div>
            <?php if (!empty($preview['calon']['files'])): ?>
              <ul class="small">
                <?php foreach ($preview['calon']['files'] as $file): ?>
                  <li><?= esc($file) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <form action="<?= base_url('admin/cleanup/delete') ?>" method="post">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-danger mt-3">Hapus File Orphan</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Hapus Data Bertahap</h5>
          </div>
          <div class="card-body">
            <p class="text-muted">Lakukan penghapusan satu per satu sesuai urutan. Setiap langkah membutuhkan konfirmasi.</p>
            <table class="table table-sm">
              <tbody>
                <tr>
                  <td>Suara</td>
                  <td><strong><?= $counts['suara'] ?></strong></td>
                </tr>
                <tr>
                  <td>Pemilih</td>
                  <td><strong><?= $counts['users'] ?></strong></td>
                </tr>
                <tr>
                  <td>Calon</td>
                  <td><strong><?= $counts['calon'] ?></strong></td>
                </tr>
                <tr>
                  <td>Kategori</td>
                  <td><strong><?= $counts['kategori'] ?></strong></td>
                </tr>
                <tr>
                  <td>Admin</td>
                  <td><strong><?= $counts['admins'] ?></strong></td>
                </tr>
              </tbody>
            </table>

            <?php if (empty($cleanupAllowedStep)): ?>
              <div class="alert alert-success mb-3">
                <strong>Semua data telah dihapus atau sudah kosong.</strong> Tidak ada langkah cleanup yang tersisa.
              </div>
            <?php endif; ?>
            <div class="accordion" id="cleanupSteps">
              <?php $steps = [
                ['name' => 'delete_votes', 'label' => 'Hapus Semua Suara', 'hint' => 'Hapus semua suara (voting) yang telah dilakukan', 'confirm' => 'HAPUS'],
                ['name' => 'delete_users', 'label' => 'Hapus Semua Pemilih', 'hint' => 'Hapus semua role user (pemilih)', 'confirm' => 'HAPUS'],
                ['name' => 'delete_calon', 'label' => 'Hapus Semua Calon', 'hint' => 'Hapus semua calon', 'confirm' => 'HAPUS'],
                ['name' => 'delete_categories', 'label' => 'Hapus Semua Kategori', 'hint' => 'Hapus kategori setelah calon sudah kosong', 'confirm' => 'HAPUS'],
                ['name' => 'delete_admins', 'label' => 'Hapus Semua Admin', 'hint' => 'Hapus semua akun admin', 'confirm' => 'HAPUS'],
              ]; ?>

              <?php foreach ($steps as $index => $step): ?>
                <?php $isOpen = $step['name'] === $cleanupOpenStep; ?>
                <?php $isAllowed = $step['name'] === $cleanupAllowedStep; ?>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading-<?= $index ?>">
                    <button class="accordion-button <?= $isOpen ? '' : 'collapsed' ?> <?= $isAllowed ? '' : 'disabled' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" aria-controls="collapse-<?= $index ?>" <?= $isAllowed ? '' : 'disabled' ?>>
                      <?= esc($step['label']) ?>
                    </button>
                  </h2>
                  <div id="collapse-<?= $index ?>" class="accordion-collapse collapse <?= $isOpen ? 'show' : '' ?>" aria-labelledby="heading-<?= $index ?>" data-bs-parent="#cleanupSteps">
                    <div class="accordion-body">
                      <p><?= esc($step['hint']) ?></p>
                      <?php if ($isAllowed): ?>
                        <form action="<?= base_url('admin/cleanup/delete-data') ?>" method="post">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="<?= esc($step['name']) ?>">
                          <?php if (in_array($step['name'], ['delete_admins','delete_users','delete_votes'])): ?>
                            <div class="mb-3">
                              <label class="form-label">Kata sandi Super Admin</label>
                              <input type="password" name="admin_password" class="form-control" required>
                            </div>
                          <?php endif; ?>
                          <div class="mb-3">
                            <label class="form-label">Ketik konfirmasi: <strong><?= esc($step['confirm']) ?></strong></label>
                            <input type="text" name="confirm_delete" class="form-control" required>
                          </div>
                          <button type="submit" class="btn btn-danger">Jalankan</button>
                        </form>
                      <?php else: ?>
                        <div class="text-muted"><em>Langkah ini akan tersedia setelah langkah sebelumnya selesai.</em></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="alert alert-secondary mt-3 mb-0">
              <strong>Lakukan semua langkah secara berurutan.</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
