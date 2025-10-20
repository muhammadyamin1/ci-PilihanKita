<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<!--begin::App Content Header-->
<div class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manajemen Calon</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manajemen</li>
                    <li class="breadcrumb-item active" aria-current="page">Calon</li>
                </ol>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>

<div class="app-content">
    <div class="container-fluid mt-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Calon
            </button>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($calon as $c): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <img src="<?= base_url($c['foto']) ?>" class="card-img-top" alt="Foto Calon">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1"><?= esc($c['nama_calon']) ?> & <?= esc($c['wakil_calon']) ?></h5>
                            <small class="text-muted"><?= esc($c['kategori']) ?></small>
                            <hr>
                            <p><strong>Visi:</strong> <?= esc($c['visi']) ?></p>
                            <p><strong>Misi:</strong> <?= esc($c['misi']) ?></p>
                            <a href="<?= base_url('admin/calon/delete/' . $c['id']) ?>"
                                onclick="return confirm('Hapus calon ini?')"
                                class="btn btn-danger btn-sm mt-2">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Calon -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formCalon" action="<?= base_url('admin/calon/save') ?>" method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Calon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Calon</label>
                        <input type="text" id="namaCalon" name="nama_calon" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Wakil Calon</label>
                        <input type="text" id="wakilCalon" name="wakil_calon" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto Calon</label>
                        <div class="input-group">
                            <input type="file" id="fotoCalon" accept="image/*" class="form-control" required>
                            <button class="btn btn-danger" type="button" id="clearFotoCalon">
                                &times;
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto Wakil Calon (opsional)</label>
                        <div class="input-group">
                            <input type="file" id="fotoWakil" accept="image/*" class="form-control">
                            <button class="btn btn-danger" type="button" id="clearFotoWakil">
                                &times;
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-center mx-auto">
                        <canvas id="previewCanvas" style="display:none; width:100%; max-width:600px; border:5px solid #ccc;" class="text-center"></canvas>
                        <div id="fileSizeInfo" class="mt-2 text-muted" style="display: none;"></div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Visi</label>
                        <textarea name="visi" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Misi</label>
                        <textarea name="misi" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kategori Pemilihan</label>
                        <select name="kategori_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="uploadBtn" class="btn btn-primary">Simpan</button>
                </div>
        </form>
    </div>
</div>
</div>

<script>
    // Load font Liberation Sans lokal
    const liberationFont = new FontFace('LiberationSans', 'url(/fonts/LiberationSans-Regular.ttf)');
    liberationFont.load().then((loadedFont) => {
        document.fonts.add(loadedFont);
        console.log('Liberation Sans siap digunakan di canvas.');
    }).catch(err => {
        console.error('Gagal load Liberation Sans:', err);
    });
</script>
<script>
    const canvas = document.getElementById('previewCanvas');
    const fileSizeInfo = document.getElementById('fileSizeInfo');
    const ctx = canvas.getContext('2d');

    function updatePreview() {
        const fileCalon = document.getElementById('fotoCalon').files[0];
        const fileWakil = document.getElementById('fotoWakil').files[0]; // opsional
        const namaCalon = document.getElementById('namaCalon').value.trim();
        const wakilCalon = document.getElementById('wakilCalon').value.trim();

        if (!fileCalon) {
            canvas.style.display = 'none';
            fileSizeInfo.style.display = 'none';
            return;
        }

        // Buat image object untuk calon
        const imgCalon = new Image();
        imgCalon.src = URL.createObjectURL(fileCalon);

        // Fungsi render canvas setelah image calon loaded
        imgCalon.onload = () => {
            // Jika ada wakil
            if (fileWakil) {
                const imgWakil = new Image();
                imgWakil.src = URL.createObjectURL(fileWakil);
                imgWakil.onload = () => drawCanvas(imgCalon, imgWakil, namaCalon, wakilCalon);
            } else {
                drawCanvas(imgCalon, null, namaCalon, wakilCalon);
            }
        };
    }

    function drawCanvas(imgCalon, imgWakil, namaCalon, wakilCalon) {
        const heightTarget = imgWakil ? Math.max(imgCalon.height, imgWakil.height) : imgCalon.height;
        const widthCalon = imgCalon.width * heightTarget / imgCalon.height;
        const widthWakil = imgWakil ? imgWakil.width * heightTarget / imgWakil.height : 0;

        canvas.width = widthCalon + widthWakil;
        canvas.height = heightTarget + 97;

        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Draw images
        ctx.drawImage(imgCalon, 0, 0, widthCalon, heightTarget);
        if (imgWakil) ctx.drawImage(imgWakil, widthCalon, 0, widthWakil, heightTarget);

        ctx.fillStyle = 'black';
        ctx.font = '40px LiberationSans';
        ctx.textAlign = 'center';

        if (imgWakil) {
            // Calon: teks di bawah foto calon (tengah foto 1)
            ctx.fillText(namaCalon, widthCalon / 2, heightTarget + 60);
            // Wakil: teks di bawah foto wakil (tengah foto 2)
            ctx.fillText(wakilCalon, widthCalon + widthWakil / 2, heightTarget + 60);
        } else {
            // Hanya calon: teks di tengah canvas
            ctx.fillText(namaCalon, widthCalon / 2, heightTarget + 60);
        }

        canvas.style.display = 'block';

        // Hitung estimasi ukuran file blob
        canvas.toBlob((blob) => {
            const sizeKB = (blob.size / 1024).toFixed(2);
            fileSizeInfo.textContent = `Estimasi ukuran file: ${sizeKB} KB`;
        }, 'image/jpeg', 0.9); // 0.9 = kualitas JPEG

        fileSizeInfo.style.display = 'block';
    }

    // Event listener: trigger hanya saat file calon/wakil berubah
    document.getElementById('fotoCalon').addEventListener('change', updatePreview);
    document.getElementById('fotoWakil').addEventListener('change', updatePreview);

    // Nama calon/wakil bisa ikut trigger jika ingin teks muncul otomatis
    document.getElementById('namaCalon').addEventListener('input', () => {
        if (document.getElementById('fotoCalon').files[0]) updatePreview();
    });
    document.getElementById('wakilCalon').addEventListener('input', () => {
        if (document.getElementById('fotoCalon').files[0]) updatePreview();
    });

    // Upload saat klik tombol Simpan
    document.getElementById('uploadBtn').addEventListener('click', () => {
        const namaCalon = document.getElementById('namaCalon').value.trim();
        const wakilCalon = document.getElementById('wakilCalon').value.trim();
        let visi = document.querySelector('textarea[name="visi"]').value.trim();
        let misi = document.querySelector('textarea[name="misi"]').value.trim();
        const kategoriId = document.querySelector('select[name="kategori_id"]').value;

        if (!namaCalon) {
            alert('Nama calon wajib diisi.');
            return;
        }

        if (!canvas || canvas.style.display === 'none') {
            alert('Silakan pilih foto calon terlebih dahulu.');
            return;
        }

        if (!visi) {
            visi = '-';
        }

        if (!misi) {
            misi = '-';
        }

        if (!kategoriId) {
            alert('Silakan pilih kategori pemilihan.');
            return;
        }

        canvas.toBlob((blob) => {
            if (!blob) {
                alert('Gagal membuat file gabungan. Pastikan foto calon valid.');
                return;
            }

            const formData = new FormData();
            formData.append('nama_calon', namaCalon);
            formData.append('wakil_calon', wakilCalon);
            formData.append('visi', visi);
            formData.append('misi', misi);
            formData.append('fotoGabungan', blob, `${namaCalon}_${wakilCalon || 'wakil'}.jpg`);
            formData.append('kategori_id', kategoriId);

            fetch('<?= base_url("admin/calon/save") ?>', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Calon berhasil ditambahkan!');
                        // bisa reset form & canvas
                        document.getElementById('formCalon').reset();
                        canvas.style.display = 'none';
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + data.error);
                    }
                })
                .catch(err => alert('Terjadi kesalahan jaringan: ' + err));
        }, 'image/jpeg', 0.9);
    });

    document.getElementById('clearFotoCalon').addEventListener('click', () => {
        const input = document.getElementById('fotoCalon');
        input.value = ''; // reset file
        updatePreview(); // update canvas
    });

    document.getElementById('clearFotoWakil').addEventListener('click', () => {
        const input = document.getElementById('fotoWakil');
        input.value = ''; // reset file
        updatePreview(); // update canvas
    });
</script>

<?= $this->endSection() ?>