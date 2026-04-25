<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content">
    <div class="container-fluid py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary mb-2">Pemilihan <?= esc($kategori_nama ?? 'Calon') ?></h2>
            <p class="lead text-muted">Pilih calon terbaik untuk masa depan kita</p>
        </div>

        <?php if ($sudahMemilih): ?>
            <div class="alert alert-success text-center py-4 fs-4">
                <i class="bi bi-check-circle-fill me-3"></i> 
                Terima kasih! Anda sudah memberikan suara.
            </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($calons as $calon): ?>
                <div class="col">
                    <div class="card h-100 shadow border-0 candidate-card overflow-hidden">
                        
                        <!-- Foto -->
                        <div class="position-relative bg-dark">
                            <?php if (!empty($calon['foto'])): ?>
                                <img src="<?= base_url('foto/calon/' . basename($calon['foto'])) ?>" 
                                     class="card-img-top w-100" 
                                     alt="<?= esc($calon['nama_calon']) ?>"
                                     style="height: 320px; object-fit: contain; background-color: #111;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-secondary" style="height: 320px;">
                                    <i class="bi bi-person-circle" style="font-size: 8rem; color: #555;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Hover Overlay -->
                            <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <button onclick="pilihCalon(<?= $calon['id'] ?>, '<?= esc($calon['nama_calon']) ?><?= !empty($calon['wakil_calon']) ? ' & ' . esc($calon['wakil_calon']) : '' ?>')" 
                                        class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow"
                                        <?= $sudahMemilih ? 'disabled' : '' ?>>
                                    <i class="bi bi-hand-thumbs-up-fill me-2"></i> PILIH CALON INI
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <!-- Nama Calon & Wakil -->
                            <h4 class="card-title fw-bold text-center mb-4">
                                <?= esc($calon['nama_calon']) ?>
                                <?php if (!empty($calon['wakil_calon'])): ?>
                                    <span class="text-primary">&amp; <?= esc($calon['wakil_calon']) ?></span>
                                <?php endif; ?>
                            </h4>

                            <!-- Tombol Visi Misi -->
                            <button class="btn btn-outline-primary btn-sm py-2 mt-auto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#visiMisiModal<?= $calon['id'] ?>">
                                <i class="bi bi-eye-fill me-1"></i> Lihat Visi & Misi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Visi & Misi - Ukuran Teks Normal & Rapi -->
<div class="modal fade" id="visiMisiModal<?= $calon['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="bi bi-info-circle me-2"></i> 
                    Visi & Misi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="line-height: 1.6;">
                
                <?php if (!empty($calon['visi'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-lightbulb-fill me-2"></i> Visi
                        </h6>
                        <p class="mb-0"><?= nl2br(esc($calon['visi'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($calon['misi'])): ?>
                    <div>
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-bullseye me-2"></i> Misi
                        </h6>
                        <p class="mb-0"><?= nl2br(esc($calon['misi'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pilihan -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Pilihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5">Apakah Anda yakin memilih</p>
                <h3 id="calonName" class="text-primary fw-bold"></h3>
                <p class="text-muted small mt-3">Pilihan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="confirmVoteBtn" class="btn btn-success px-5">Ya, Pilih</button>
            </div>
        </div>
    </div>
</div>

<style>
.candidate-card {
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    border-radius: 16px;
}

.candidate-card:hover {
    transform: scale(1.05) translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.hover-overlay {
    background: rgba(0, 0, 0, 0.35);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.candidate-card:hover .hover-overlay {
    opacity: 1;
}
#visiMisiModal .modal-body p {
    font-size: 1rem;           /* ukuran normal */
    line-height: 1.65;
    color: #333;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
let selectedCalonId = null;

function pilihCalon(id, namaLengkap) {
    selectedCalonId = id;
    document.getElementById('calonName').textContent = namaLengkap;
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

document.getElementById('confirmVoteBtn').addEventListener('click', function() {
    if (!selectedCalonId) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

    fetch('<?= base_url('user/vote') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'calon_id=' + selectedCalonId + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            confetti({ particleCount: 300, spread: 80, origin: { y: 0.6 } });
            setTimeout(() => location.reload(), 1800);
        } else {
            alert(data.message || 'Gagal memilih');
        }
    })
    .catch(() => alert('Terjadi kesalahan'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Ya, Pilih';
    });
});
</script>

<?= $this->endSection(); ?>