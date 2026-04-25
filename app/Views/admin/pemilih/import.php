<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Import Pemilih dari CSV</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <strong>Petunjuk:</strong><br>
                    1. Download template CSV di bawah ini<br>
                    2. Isi data sesuai kolom (Password boleh dikosongkan)<br>
                    3. Upload file CSV menggunakan form di bawah
                </div>

                <a href="<?= base_url('admin/pemilih/download-template') ?>" class="btn btn-info mb-4">
                    <i class="bi bi-download"></i> Download Template CSV
                </a>

                <form action="<?= base_url('admin/pemilih/import-process') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Pilih Kategori</label>
                        <select name="kategori_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File CSV / Excel</label>
                        <input type="file" name="file_excel" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Format yang direkomendasikan: CSV dengan pemisah semicolon (;)</small>
                    </div>

                    <button type="submit" class="btn btn-success">Import Data Pemilih</button>
                    <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>