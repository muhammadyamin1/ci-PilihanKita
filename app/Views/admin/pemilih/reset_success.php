<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!-- Modal Popup Password -->
<div class="modal fade show" id="passwordModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="passwordModalLabel" aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="passwordModalLabel">
                    <i class="bi bi-check-circle me-2"></i>Password Berhasil Direset
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-key-fill text-success" style="font-size: 3rem;"></i>
                </div>
                
                <div class="alert alert-info">
                    <strong>Informasi User:</strong><br>
                    Username: <strong><?= esc($user['username']) ?></strong><br>
                    Nama: <strong><?= esc($user['nama']) ?></strong>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password Baru:</label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold text-primary" id="newPassword" value="<?= esc($new_password) ?>" readonly onclick="this.select()">
                        <button class="btn btn-outline-secondary" type="button" onclick="salinPassword()">
                            <i class="bi bi-clipboard"></i> Salin
                        </button>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Catatan:</strong> Password ini hanya ditampilkan sekali. Pastikan untuk menyalin atau memberitahu user sebelum menutup popup ini.
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('admin/pemilih') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pemilih
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Backdrop for modal -->
<div class="modal-backdrop fade show"></div>

<script>
function salinPassword() {
    var copyText = document.getElementById('newPassword');
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    
    // Try navigator.clipboard first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyText.value).then(function() {
            showCopiedFeedback();
        }).catch(function() {
            // Fallback to execCommand
            document.execCommand('copy');
            showCopiedFeedback();
        });
    } else {
        // Fallback for older browsers
        document.execCommand('copy');
        showCopiedFeedback();
    }
}

function showCopiedFeedback() {
    var btn = document.querySelector('#newPassword + button');
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Tersalin!';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');
    
    setTimeout(function() {
        btn.innerHTML = '<i class="bi bi-clipboard"></i> Salin';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>

<?= $this->endSection(); ?>