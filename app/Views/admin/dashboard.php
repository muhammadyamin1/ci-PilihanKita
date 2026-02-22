<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<!--begin::App Content Header-->
<div class="app-content-header">
  <!--begin::Container-->
  <div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Dashboard</h3>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </div>
    </div>
    <!--end::Row-->
  </div>
  <!--end::Container-->
</div>

<div class="app-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 mb-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="mb-2 fw-bold">Suara Masuk</h6>
            <div class="progress-bar bg-success"
              role="progressbar"
              style="width: <?= $partisipasi ?>%;"
              aria-valuenow="<?= $partisipasi ?>"
              aria-valuemin="0"
              aria-valuemax="100">
              <?= $partisipasi ?>%
            </div>
            <small class="text-muted d-block mt-2">
              <?= $suaraMasuk ?> dari <?= $totalPemilih ?> pemilih
            </small>
          </div>
        </div>
      </div>
    </div>
    <!-- Baris Statistik Utama -->
    <div class="row mb-2">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-primary shadow-sm">
            <i class="bi bi-person-badge-fill"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Total Calon</span>
            <span class="info-box-number"><?= $totalCalon ?></span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-success shadow-sm">
            <i class="bi bi-people-fill"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Total Pemilih</span>
            <span class="info-box-number"><?= $totalPemilih ?></span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-warning shadow-sm">
            <i class="bi bi-envelope-paper-fill"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Suara Masuk</span>
            <span class="info-box-number"><?= $suaraMasuk ?></span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
          <span class="info-box-icon text-bg-danger shadow-sm">
            <i class="bi bi-bar-chart-fill"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Partisipasi</span>
            <span class="info-box-number">
              <?= $partisipasi ?><small>%</small>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Baris Chart Utama -->
    <div class="row">
      <!-- Pie Chart -->
      <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">
              Persentase Suara - <?= esc($kategori['nama']) ?>
            </h5>
          </div>
          <div class="card-body">
            <canvas id="pieChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Daftar Calon -->
      <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">Daftar Calon</h5>
          </div>
          <div class="card-body">
            <ul class="list-group"></ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Bar Chart -->
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">Jumlah Suara per Calon</h5>
          </div>
          <div class="card-body">
            <canvas id="barChart"></canvas>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Chart.js -->
<script src="<?= base_url('js/chart.js') ?>"></script>
<script src="<?= base_url('js/chartjs-plugin-datalabels.js') ?>"></script>
<script>
  const calon = <?= json_encode(array_map(function ($row, $i) {

                  $warna = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#20c997', '#fd7e14'];

                  return [
                    'nama'  => $row['nama_calon'],
                    'suara' => (int)$row['total_suara'],
                    'warna' => $warna[$i % count($warna)]
                  ];
                }, $hasil, array_keys($hasil))) ?>;

  const totalSuara = calon.reduce((a, b) => a + b.suara, 0);
  const persen = calon.map(c =>
    totalSuara > 0 ?
    ((c.suara / totalSuara) * 100).toFixed(1) :
    0
  );

  // PIE CHART
  const ctx = document.getElementById('pieChart').getContext('2d');
  const hasilPie = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: calon.map(c => c.nama),
      datasets: [{
        data: calon.map(c => c.suara),
        backgroundColor: calon.map(c => c.warna)
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: (context) => {
              const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
              const value = context.parsed;
              const percentage = ((value / total) * 100).toFixed(1) + '%';
              return `${context.label}: ${value} (${percentage})`;
            }
          }
        },
        datalabels: { // plugin tambahan
          color: '#fff',
          formatter: (value, context) => {
            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            const percentage = ((value / total) * 100).toFixed(1);
            return percentage + '%';
          }
        }
      }
    },
    plugins: [ChartDataLabels]
  });

  // BAR CHART
  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: calon.map(c => c.nama),
      datasets: [{
        label: 'Jumlah Suara',
        data: calon.map(c => c.suara),
        backgroundColor: calon.map(c => c.warna),
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Daftar calon di kanan
  const listGroup = document.querySelector('.list-group');
  listGroup.innerHTML = calon.map((c, i) => `
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span><i class="bi bi-person-circle me-2" style="color:${c.warna}"></i> <strong>${c.nama}</strong></span>
      <span class="badge rounded-pill" style="background:${c.warna}">${persen[i]}%</span>
    </li>
  `).join('');
</script>

<?= $this->endSection(); ?>