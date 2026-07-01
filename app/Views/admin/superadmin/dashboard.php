<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Dashboard Super Admin</h3>
        <p class="text-muted">Ringkasan global dan pilih kategori untuk melihat detail.</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard Super Admin</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    <div class="row mb-3">
      <div class="col-md-2">
        <div class="info-box">
          <span class="info-box-icon text-bg-primary shadow-sm"><i class="bi bi-list-columns"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Kategori</span>
            <span class="info-box-number"><?= $totalCategories ?></span>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="info-box">
          <span class="info-box-icon text-bg-primary shadow-sm"><i class="bi bi-person-badge-fill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Calon</span>
            <span class="info-box-number"><?= $totalCalon ?></span>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="info-box">
          <span class="info-box-icon text-bg-success shadow-sm"><i class="bi bi-people-fill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Pemilih</span>
            <span class="info-box-number"><?= $totalPemilih ?></span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-warning shadow-sm"><i class="bi bi-envelope-paper-fill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Suara Masuk</span>
            <span class="info-box-number"><?= $totalSuara ?></span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-danger shadow-sm"><i class="bi bi-bar-chart-fill"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Rata-rata Partisipasi</span>
            <span class="info-box-number"><?= $avgPartisipasi ?>%</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="row g-2">
              <div class="col-12 col-sm-6">
                <label class="form-label">Scope</label>
                <select id="scopeSelect" class="form-select">
                  <option value="global">Global</option>
                  <option value="per-admin">Per Admin</option>
                </select>
              </div>
              <div class="col-12 col-sm-6" id="adminSearchWrap" style="display:none; position:relative;">
                <label class="form-label">Pilih Admin</label>
                <select id="adminSelect" class="form-select" style="width:100%"></select>
              </div>
            </div>
            <small class="text-muted d-block mt-2">Atau pilih kategori dari Top 10 di kanan.</small>
          </div>
        </div>

        <div class="card shadow-sm mt-3" id="categoryDetailCard" style="display:none;">
          <div class="card-header bg-light"><h5 class="mb-0" id="categoryTitle"></h5></div>
          <div class="card-body">
            <div class="mb-3">
              <h6>Partisipasi</h6>
              <div class="progress" style="height:20px;">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width:0%">0%</div>
              </div>
              <small id="detailCounts" class="text-muted"></small>
            </div>
            <div>
              <canvas id="pieChartAdmin"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-light"><h5 class="mb-0">Top 10 Kategori (by suara)</h5></div>
          <div class="card-body">
            <ul class="list-group" id="topCategoriesList">
              <?php foreach ($topCategories as $cat): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center top-cat-item" data-id="<?= $cat['id'] ?>">
                  <div>
                    <div class="fw-bold"><?= esc($cat['nama']) ?></div>
                    <small class="text-muted">Admin: <?= esc($cat['admin_name'] ?? '-') ?></small>
                  </div>
                  <span class="badge bg-primary rounded-pill"><?= $cat['total_suara'] ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="<?= base_url('js/chart.js') ?>"></script>
<!-- jQuery + Select2 (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  // make category items show pointer and tweak Select2 appearance to match Bootstrap 5
  const styleEl = document.createElement('style');
  styleEl.innerHTML = `
    .top-cat-item{cursor:pointer;}
    #adminSuggestions .admin-suggestion{cursor:pointer;}

    /* Select2 single selection styling to match Bootstrap 5 form controls */
    .select2-container--default .select2-selection--single {
      background-color: #fff !important;
      border: 1px solid #ced4da !important;
      color: #212529 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #212529 !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #212529 transparent transparent !important; }
    .select2-container--default .select2-selection--single:focus, .select2-container--default .select2-selection--single:hover { border-color: #86b7fe !important; box-shadow: 0 0 0 .25rem rgba(13,110,253,.25) !important; }
    .select2-container--default .select2-dropdown { background: #fff !important; color: #212529 !important; }
    .select2-container--default .select2-search--dropdown .select2-search__field,
    .select2-container--default .select2-search--inline .select2-search__field {
      background-color: #fff !important;
      color: #212529 !important;
      border: 1px solid #ced4da !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus,
    .select2-container--default .select2-search--inline .select2-search__field:focus {
      outline: none !important;
      box-shadow: none !important;
    }
    .select2-container--default .select2-results__option--highlighted,
    .select2-container--default .select2-results__option[aria-selected="true"] {
      background-color: #e9ecef !important;
      color: #212529 !important;
    }
    .top-cat-item.loading { opacity: 0.7; pointer-events: none; }
    .top-cat-item .top-cat-spinner { margin-left: 0.75rem; }
  `;
  document.head.appendChild(styleEl);

  function setTopCategoryLoading(id, loading) {
    const item = document.querySelector(`.top-cat-item[data-id="${id}"]`);
    if (!item) return;

    if (loading) {
      item.classList.add('loading');
      if (!item.querySelector('.top-cat-spinner')) {
        const spinner = document.createElement('span');
        spinner.className = 'spinner-border spinner-border-sm text-primary top-cat-spinner';
        spinner.role = 'status';
        spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
        item.querySelector('.fw-bold').after(spinner);
      }
    } else {
      item.classList.remove('loading');
      const spinner = item.querySelector('.top-cat-spinner');
      if (spinner) spinner.remove();
    }
  }

  // helper to fetch category stats and render chart
  async function loadCategory(id) {
    setTopCategoryLoading(id, true);
    const res = await fetch('<?= base_url('superadmin/category') ?>/' + id);
    if (!res.ok) {
      setTopCategoryLoading(id, false);
      return;
    }
    const data = await res.json();

    document.getElementById('categoryDetailCard').style.display = 'block';
    document.getElementById('categoryTitle').innerText = data.nama || ('Kategori ' + id);

    const part = data.partisipasi;
    const bar = document.getElementById('progressBar');
    bar.style.width = part + '%';
    bar.innerText = part + '%';

    document.getElementById('detailCounts').innerText = `${data.suaraMasuk} suara dari ${data.totalPemilih} pemilih. Total calon: ${data.totalCalon}`;

    // build chart data
    const labels = data.hasil.map(r => r.nama_calon);
    const values = data.hasil.map(r => parseInt(r.total_suara, 10));
    const colors = labels.map((_, i) => ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#20c997', '#fd7e14'][i % 7]);

    const ctx = document.getElementById('pieChartAdmin').getContext('2d');
    if (window._adminPie) window._adminPie.destroy();
    window._adminPie = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{ data: values, backgroundColor: colors }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    setTopCategoryLoading(id, false);
  }

  // Click handlers for top list (will be rebound after dynamic load)
  function bindTopListClicks() {
    document.querySelectorAll('.top-cat-item').forEach(el => {
      el.onclick = null;
      const categoryId = el.dataset.id;
      el.addEventListener('click', () => loadCategory(categoryId));
    });
  }

  bindTopListClicks();

  // Scope and admin select handling using Select2
  const scopeSelect = document.getElementById('scopeSelect');
  const adminSearchWrap = document.getElementById('adminSearchWrap');
  let selectedAdminId = null;

  scopeSelect.addEventListener('change', () => {
    const v = scopeSelect.value;
    if (v === 'per-admin') {
      adminSearchWrap.style.display = 'block';
    } else {
      adminSearchWrap.style.display = 'none';
      selectedAdminId = null;
      loadTopCategories();
    }
  });

  // initialize Select2
  $(document).ready(function() {
    $('#adminSelect').select2({
      placeholder: 'Cari admin... (ketik minimal 2 karakter)',
      minimumInputLength: 2,
      allowClear: true,
      ajax: {
        url: '<?= base_url('superadmin/admins/list') ?>',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return { q: params.term };
        },
        processResults: function(data) {
          return { results: data };
        },
        cache: true
      },
      width: 'resolve'
    });

    $('#adminSelect').on('select2:select', function(e) {
      selectedAdminId = e.params.data.id;
      loadTopCategories(selectedAdminId);
    });

    $('#adminSelect').on('select2:clear', function() {
      selectedAdminId = null;
      loadTopCategories();
    });
  });

  // Load top categories, optionally filtered by admin
  async function loadTopCategories(adminId) {
    const url = '<?= base_url('superadmin/categories/top') ?>' + (adminId ? '?admin_id=' + adminId : '');
    const res = await fetch(url);
    if (!res.ok) return;
    const rows = await res.json();
    const list = document.getElementById('topCategoriesList');
    list.innerHTML = rows.map(r => `<li class="list-group-item d-flex justify-content-between align-items-center top-cat-item" data-id="${r.id}">
          <div>
            <div class="fw-bold">${r.nama}</div>
            <small class="text-muted">Admin: ${r.admin_name || '-'}</small>
          </div>
          <span class="badge bg-primary rounded-pill">${r.total_suara}</span>
        </li>`).join('');
    bindTopListClicks();
  }
</script>

<?= $this->endSection(); ?>