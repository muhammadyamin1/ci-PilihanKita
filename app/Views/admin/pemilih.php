<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Pemilih</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manajemen</li>
                    <li class="breadcrumb-item active" aria-current="page">Pemilih</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<div class="app-content">

    <div class="container-fluid">
        <div class="text-end mb-3">

            <div class="btn-group">
                <a href="<?= base_url('pemilihan/tambah') ?>" class="btn btn-primary btn-sm">
                    + Tambah Manual
                </a>
                <a href="<?= base_url('pemilihan/generate') ?>" class="btn btn-success btn-sm">
                    Generate Otomatis
                </a>
                <a href="<?= base_url('pemilihan/import') ?>" class="btn btn-warning btn-sm">
                    Impor Excel
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Generated</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)) : ?>
                            <?php $no = 1;
                            foreach ($users as $u) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= esc($u['username']); ?></td>
                                    <td><?= esc($u['nama']); ?></td>
                                    <td><?= $u['email'] ?? '-'; ?></td>

                                    <td class="text-center">
                                        <?php if ($u['generated']) : ?>
                                            <span class="badge bg-success">Ya</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary">Manual</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($u['sudah_memilih']) : ?>
                                            <span class="badge bg-primary">Sudah Memilih</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark">Belum</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?= base_url('pemilihan/edit/' . $u['id']) ?>"
                                            class="btn btn-sm btn-outline-info">
                                            Edit
                                        </a>
                                        <a href="<?= base_url('pemilihan/reset/' . $u['id']) ?>"
                                            class="btn btn-sm btn-outline-secondary">
                                            Reset
                                        </a>
                                        <a href="<?= base_url('pemilihan/hapus/' . $u['id']) ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Yakin hapus user ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Belum ada data pemilih
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>