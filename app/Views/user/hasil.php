<?= $this->extend('layout/main'); ?>
<?= $this->section('content'); ?>

<div class="app-content-header">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6">
        <h3 class="mb-0">Hasil Suara</h3>
      </div>
    </div>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-12 mb-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="fw-bold mb-1"><?= esc($kategori['nama'] ?? 'Pemilihan') ?></h5>
            <p class="text-muted mb-3">Hasil suara terkini untuk kategori pemilihan Anda.</p>
            <div class="progress" style="height: 20.5px;">
              <div class="progress-bar bg-success <?= $partisipasi < 100 ? 'progress-bar-striped progress-bar-animated' : '' ?>"
                role="progressbar"
                style="width: <?= $partisipasi ?>%;"
                aria-valuenow="<?= $partisipasi ?>"
                aria-valuemin="0"
                aria-valuemax="100">
                <?= $partisipasi ?>%
              </div>
            </div>
            <small class="text-muted d-block mt-2">
              <?= $suaraMasuk ?> dari <?= $totalPemilih ?> pemilih sudah memberikan suara
            </small>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">Persentase Suara</h5>
          </div>
          <div class="card-body">
            <canvas id="pieChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">Perolehan Calon</h5>
          </div>
          <div class="card-body">
            <ul class="list-group" id="hasilList"></ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h5 class="card-title mb-0">Jumlah Suara per Calon</h5>
          </div>
          <div class="card-body" style="height: 420px;">
            <canvas id="barChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url('js/chart.js') ?>"></script>
<script src="<?= base_url('js/chartjs-plugin-datalabels.js') ?>"></script>
<script>
  const calon = <?= json_encode(array_map(function ($row, $i) {
                  $warna = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#20c997', '#fd7e14'];

                  return [
                    'nama' => $row['nama_calon'] . (!empty($row['wakil_calon']) ? ' & ' . $row['wakil_calon'] : ''),
                    'suara' => (int) $row['total_suara'],
                    'warna' => $warna[$i % count($warna)]
                  ];
                }, $hasil, array_keys($hasil))) ?>;

  const totalSuara = calon.reduce((total, item) => total + item.suara, 0);
  const persen = calon.map(item => totalSuara > 0 ? ((item.suara / totalSuara) * 100).toFixed(1) : '0.0');
  const pieCanvas = document.getElementById('pieChart');
  const barCanvas = document.getElementById('barChart');
  const listGroup = document.getElementById('hasilList');

  listGroup.innerHTML = calon.length ? calon.map((item, index) => `
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span><i class="bi bi-person-circle me-2" style="color:${item.warna}"></i><strong>${item.nama}</strong></span>
      <span class="badge rounded-pill" style="background:${item.warna}">${item.suara} suara (${persen[index]}%)</span>
    </li>
  `).join('') : '<li class="list-group-item text-muted">Belum ada calon.</li>';

  if (calon.length) {
    new Chart(pieCanvas, {
      type: 'pie',
      data: {
        labels: calon.map(item => item.nama),
        datasets: [{
          data: calon.map(item => item.suara),
          backgroundColor: calon.map(item => item.warna)
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom'
          },
          datalabels: {
            color: '#fff',
            formatter: value => totalSuara > 0 ? ((value / totalSuara) * 100).toFixed(1) + '%' : '0%'
          }
        }
      },
      plugins: [ChartDataLabels]
    });

    new Chart(barCanvas, {
      type: 'bar',
      data: {
        labels: calon.map(item => item.nama),
        datasets: [{
          label: 'Jumlah Suara',
          data: calon.map(item => item.suara),
          backgroundColor: calon.map(item => item.warna),
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }
</script>

<?= $this->endSection(); ?>
