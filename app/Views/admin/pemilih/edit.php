<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Edit Pemilih</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pemilih') ?>">Pemilih</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="app-content">

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Edit Pemilih</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $error   = session()->getFlashdata('error');
                        $success = session()->getFlashdata('success');
                        ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= esc($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= esc($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('admin/pemilih/update/' . $user['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= esc($user['username']) ?>" disabled>
                                <div class="form-text">Username tidak dapat diubah.</div>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" id="nama" class="form-control" value="<?= esc($user['nama']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" placeholder="Opsional (Tidak Wajib)">
                            </div>

                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori Pemilihan</label>
                                <select name="kategori_id" id="kategori_id" class="form-select">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= $k['id'] == $user['kategori_id'] ? 'selected' : '' ?>>
                                            <?= esc($k['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>