<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
    .card:hover {
        transform: scale(1.02);
        transition: 0.2s;
    }

    #previewCanvas {
        display: block;
        max-width: 100%;
        max-height: 300px;
        width: auto;
        height: auto;
        border: 5px solid #ccc;
        margin: 0 auto;
    }
</style>

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

        <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
            <?php
            $type = session()->getFlashdata('success') ? 'success' : 'danger';
            $message = session()->getFlashdata('success') ?? session()->getFlashdata('error');
            ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert" id="flashdataAlert">
                <?= esc($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row" id="calonList">
            <?php foreach ($calon as $c): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <img src="<?= base_url('foto/calon/' . basename($c['foto'])) ?>"
                            class="card-img-top"
                            alt="Foto Calon"
                            style="height: 222.6px; object-fit: contain; background-color: #212529; cursor: pointer;"
                            data-bs-toggle="modal"
                            data-bs-target="#fotoModal<?= $c['id'] ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <?= esc($c['nama_calon']) ?>
                                    <?= !empty($c['wakil_calon']) ? ' & ' . esc($c['wakil_calon']) : '' ?>
                                </h6>
                                <span class="badge bg-success"><?= esc($c['kategori']) ?></span>
                            </div>
                            <hr style="margin: 6px 0px;">
                            <?php if (!empty($c['visi'])): ?>
                                <p class="mb-1"><strong>Visi:</strong><br><?= nl2br(esc($c['visi'])) ?></p>
                                <hr style="margin: 6px 0;">
                            <?php endif; ?>
                            <?php if (!empty($c['misi'])): ?>
                                <p class="mb-1"><strong>Misi:</strong><br><?= nl2br(esc($c['misi'])) ?></p>
                                <hr style="margin: 6px 0;">
                            <?php endif; ?>
                            <div class="d-flex justify-content-end mt-3">
                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm me-1 btnEditCalon" data-id="<?= $c['id'] ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="<?= base_url('admin/calon/delete/' . $c['id']) ?>"
                                    onclick="return confirm('Hapus calon ini?')"
                                    class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="fotoModal<?= $c['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: calc(68vh * 1.346);">
                            <div class="modal-content bg-dark">
                                <div class="modal-body p-0 text-center">
                                    <img src="<?= base_url('foto/calon/' . basename($c['foto'])) ?>"
                                        alt="Foto Calon"
                                        class="img-fluid"
                                        style="max-height: 68vh; object-fit: contain;">
                                </div>
                            </div>
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
                    <input type="hidden" id="editId" name="id">
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
                        <canvas id="previewCanvas" style="display: none;"></canvas>
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
<!-- End Modal Tambah Calon -->

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
    let compressedBlob = null;

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
        const spacing = 20; // jarak antara calon dan wakil jika keduanya ada
        const textHeight = 62; // tinggi teks di bawah foto
        const padding = 10; // padding di dalam bingkai

        const heightTarget = imgWakil ? Math.max(imgCalon.height, imgWakil.height) : imgCalon.height;
        const widthCalon = imgCalon.width * heightTarget / imgCalon.height;
        const widthWakil = imgWakil ? imgWakil.width * heightTarget / imgWakil.height : 0;

        canvas.width = widthCalon + (imgWakil ? widthWakil + spacing : 0) + padding * 2;
        canvas.height = heightTarget + textHeight + padding * 3;

        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // koordinat x untuk calon dan wakil
        let xCalon = padding;
        let xWakil = imgWakil ? xCalon + widthCalon + spacing : 0;

        // Draw images
        ctx.drawImage(imgCalon, xCalon, padding, widthCalon, heightTarget);
        if (imgWakil) ctx.drawImage(imgWakil, xWakil, padding, widthWakil, heightTarget);

        // Draw frames / bingkai
        ctx.lineWidth = 4;
        ctx.strokeStyle = '#000'; // warna bingkai hitam
        ctx.strokeRect(xCalon - 5, padding - 5, widthCalon + 10, heightTarget + textHeight + 20);
        if (imgWakil) {
            ctx.strokeRect(xWakil - 5, padding - 5, widthWakil + 10, heightTarget + textHeight + 20);
        }

        // Draw text
        ctx.fillStyle = 'black';
        ctx.font = '40px LiberationSans';
        ctx.textAlign = 'center';

        // teks di bawah calon
        ctx.fillText(namaCalon, xCalon + widthCalon / 2, heightTarget + padding + 50);
        if (imgWakil) {
            ctx.fillText(wakilCalon, xWakil + widthWakil / 2, heightTarget + padding + 50);
        }

        canvas.style.display = 'block';
    }

    function compressToUnder1MB(canvas, callback) {
        let quality = 0.9;

        function tryCompress() {
            canvas.toBlob((blob) => {
                const sizeKB = blob.size / 1024;
                const sizeMB = sizeKB / 1024;

                if (sizeMB > 1 && quality > 0.2) {
                    // Turunkan kualitas bertahap
                    quality -= 0.1;
                    tryCompress();
                } else {
                    compressedBlob = blob;
                    // Selesai, tampilkan info ukuran akhir
                    fileSizeInfo.textContent = `Estimasi ukuran file: ${sizeKB.toFixed(2)} KB (Quality ${quality.toFixed(1)})`;
                    fileSizeInfo.style.display = 'block';
                    console.log(`Final size: ${sizeKB.toFixed(2)} KB, Quality: ${quality.toFixed(1)}`);
                    if (typeof callback === 'function') {
                        callback(blob);
                    }
                }
            }, 'image/jpeg', quality);
        }

        tryCompress();
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
    const uploadBtn = document.getElementById('uploadBtn');

    uploadBtn.addEventListener('click', () => {
        if (uploadBtn.disabled) return; // mencegah klik ganda sebelum validasi dijalankan

        const namaCalon = document.getElementById('namaCalon').value.trim();
        const wakilCalon = document.getElementById('wakilCalon').value.trim();
        const fileCalon = document.getElementById('fotoCalon').files[0];
        const fileWakil = document.getElementById('fotoWakil').files[0];
        let visi = document.querySelector('textarea[name="visi"]').value.trim();
        let misi = document.querySelector('textarea[name="misi"]').value.trim();
        const kategoriId = document.querySelector('select[name="kategori_id"]').value;
        const idEdit = document.getElementById('editId').value;
        const url = idEdit ? '<?= base_url("admin/calon/update") ?>' : '<?= base_url("admin/calon/save") ?>';

        if (!namaCalon) {
            alert('Nama calon wajib diisi.');
            return;
        }

        if (!idEdit) {
            if (fileWakil && !wakilCalon) {
                alert('Nama wakil calon wajib diisi jika foto wakil diunggah.');
                return;
            }

            if (!fileWakil && wakilCalon) {
                alert('File wakil calon wajib diunggah jika nama wakil diisi.');
                return;
            }

            if (!canvas || canvas.style.display === 'none') {
                alert('Silakan pilih foto calon terlebih dahulu.');
                return;
            }
        }

        if (!kategoriId) {
            alert('Silakan pilih kategori pemilihan.');
            return;
        }

        // Mencegah klik ganda
        uploadBtn.disabled = true;
        const originalText = uploadBtn.textContent;
        uploadBtn.textContent = "Menyimpan...";

        if (idEdit && !fileCalon && !fileWakil) {
            const formData = new FormData();
            formData.append('id', idEdit);
            formData.append('nama_calon', namaCalon);
            formData.append('wakil_calon', wakilCalon);
            formData.append('visi', visi || '');
            formData.append('misi', misi || '');
            formData.append('kategori_id', kategoriId);

            fetch(url, {
                    method: 'POST',
                    body: formData
                }).then(res => res.json())
                .then(handleResponse)
                .catch(handleError);
        } else {
            compressToUnder1MB(canvas, (blob) => {
                if (!blob) {
                    alert('Gagal membuat file gabungan. Pastikan foto calon valid.');
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = originalText;
                    return;
                }
                const formData = new FormData();
                formData.append('nama_calon', namaCalon);
                formData.append('wakil_calon', wakilCalon);
                formData.append('visi', visi || '');
                formData.append('misi', misi || '');
                formData.append('fotoGabungan', blob, `${namaCalon}_${wakilCalon || 'wakil'}.jpg`);
                formData.append('kategori_id', kategoriId);
                formData.append('id', idEdit);

                fetch(url, {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json())
                    .then(handleResponse)
                    .catch(handleError);
            });
        }

        function handleResponse(data) {
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;

            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambah'));
                modal.hide();

                if (idEdit) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perubahan berhasil disimpan!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1600);
                } else {
                    // Tutup modal Bootstrap
                    modal.hide();

                    // Buat card baru sesuai HTML-mu
                    const newCard = document.createElement('div');
                    newCard.classList.add('col-md-4', 'mb-4');

                    const fotoUrl = data.newCalon.foto;
                    const id = data.newCalon.id;
                    const namaCalon = data.newCalon.nama_calon;
                    const wakilCalon = data.newCalon.wakil_calon;
                    const kategori = data.newCalon.kategori;
                    const visi = data.newCalon.visi || '';
                    const misi = data.newCalon.misi || '';

                    newCard.innerHTML = `
                            <div class="card shadow-sm border-0">
                                <img src="${fotoUrl}"
                                    class="card-img-top"
                                    alt="Foto Calon"
                                    style="height: 222.6px; object-fit: contain; background-color: #212529; cursor: pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#fotoModal${id}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            ${namaCalon}${wakilCalon ? ' & ' + wakilCalon : ''}
                                        </h6>
                                        <span class="badge bg-success">${kategori}</span>
                                    </div>
                                    <hr style="margin: 6px 0px;">
                                    ${visi ? `<p class="mb-1"><strong>Visi:</strong><br>${visi.replace(/\n/g, '<br>')}</p><hr style="margin: 6px 0;">` : ''}
                                    ${misi ? `<p class="mb-1"><strong>Misi:</strong><br>${misi.replace(/\n/g, '<br>')}</p><hr style="margin: 6px 0;">` : ''}
                                    <div class="d-flex justify-content-end mt-3">
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm me-1 btnEditCalon" data-id="${id}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="admin/calon/delete/${id}" 
                                        onclick="return confirm('Hapus calon ini?')" 
                                        class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="fotoModal${id}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="max-width: calc(68vh * 1.346);">
                                    <div class="modal-content bg-dark">
                                        <div class="modal-body p-0 text-center">
                                            <img src="${fotoUrl}"
                                                alt="Foto Calon"
                                                class="img-fluid"
                                                style="max-height: 68vh; object-fit: contain;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    // Tambahkan card ke dalam daftar setelah header
                    document.getElementById('calonList').appendChild(newCard);

                    // Reset form & canvas
                    document.getElementById('formCalon').reset();
                    canvas.style.display = 'none';
                    fileSizeInfo.style.display = 'none';

                    Swal.fire({
                        icon: 'success',
                        title: 'Calon berhasil ditambahkan!',
                        showConfirmButton: false,
                        timer: 1800
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan calon',
                    text: data.error || 'Terjadi kesalahan tak terduga.'
                });
            }
        }

        function handleError(err) {
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;
            alert('Terjadi kesalahan jaringan: ' + err);
        }

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

    // Saat klik tombol Edit
    document.querySelectorAll('.btnEditCalon').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`<?= base_url('admin/calon/get/') ?>${id}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const data = res.data;
                        document.getElementById('editId').value = data.id;
                        document.getElementById('namaCalon').value = data.nama_calon;
                        document.getElementById('wakilCalon').value = data.wakil_calon || '';
                        document.querySelector('textarea[name="visi"]').value = data.visi || '';
                        document.querySelector('textarea[name="misi"]').value = data.misi || '';
                        document.querySelector('select[name="kategori_id"]').value = data.kategori_id;

                        document.querySelector('.modal-title').textContent = 'Edit Calon';
                        document.getElementById('uploadBtn').textContent = 'Simpan Perubahan';
                        new bootstrap.Modal(document.getElementById('modalTambah')).show();
                    } else {
                        alert('Gagal mengambil data calon.');
                    }
                });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alertBox = document.getElementById('flashdataAlert');
        if (alertBox) {
            const timeout = alertBox.classList.contains('alert-success') ? 4000 : 6000;

            setTimeout(() => {
                const alert = bootstrap.Alert.getOrCreateInstance(alertBox);
                alert.close();
            }, timeout);
        }
    });
</script>

<?= $this->endSection() ?>