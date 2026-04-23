<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Generate Pemilih Otomatis</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pemilih') ?>">Pemilih</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generate</li>
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
                        <h5 class="card-title mb-0">Form Generate Pemilih</h5>
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

                        <form action="<?= base_url('admin/pemilih/generate') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori Pemilihan</label>
                                <select name="kategori_id" id="kategori_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= $k['aktif'] ? 'selected' : '' ?>>
                                            <?= esc($k['nama']) ?> <?= $k['aktif'] ? '(Aktif)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Pemilih</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" max="100" value="10" required>
                                <div class="form-text">Maksimal 100 pemilih per generate.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-success">Generate Pemilih</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>