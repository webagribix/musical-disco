<?php
/** @var int $totalLive @var int $todayMortality @var int $todayEggs @var int $pendingVaccinations @var int $activeAlerts @var int $activeBatches @var array $envLogs @var array $todayTasks @var string $eggChart @var string $feedChart */
include __DIR__ . '/../layout/nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — <?= APP_NAME ?></title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2e7d32">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <h4 class="fw-bold mb-3"><i class="bi bi-speedometer2"></i> Dashboard</h4>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
      <div class="card text-center border-success">
        <div class="card-body py-2">
          <div class="display-6 fw-bold text-success"><?= number_format($totalLive) ?></div>
          <small class="text-muted">Live Birds</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-danger">
        <div class="card-body py-2">
          <div class="display-6 fw-bold text-danger"><?= number_format($todayMortality) ?></div>
          <small class="text-muted">Today Mortality</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-warning">
        <div class="card-body py-2">
          <div class="display-6 fw-bold text-warning"><?= number_format($todayEggs) ?></div>
          <small class="text-muted">Today Eggs</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-info">
        <div class="card-body py-2">
          <div class="display-6 fw-bold text-info"><?= number_format($pendingVaccinations) ?></div>
          <small class="text-muted">Pending Vaccines</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center <?= $activeAlerts > 0 ? 'border-danger' : 'border-secondary' ?>">
        <div class="card-body py-2">
          <div class="display-6 fw-bold <?= $activeAlerts > 0 ? 'text-danger' : 'text-secondary' ?>"><?= $activeAlerts ?></div>
          <small class="text-muted">Active Alerts</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-primary">
        <div class="card-body py-2">
          <div class="display-6 fw-bold text-primary"><?= $activeBatches ?></div>
          <small class="text-muted">Active Batches</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <!-- Egg Chart -->
    <div class="col-md-8 advanced-only">
      <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-egg"></i> Egg Production (Last 30 Days)</div>
        <div class="card-body"><canvas id="eggChart" height="120"></canvas></div>
      </div>
    </div>
    <!-- Farm Conditions -->
    <div class="col-md-4">
      <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-thermometer-half"></i> Farm Conditions</div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead><tr><th>House</th><th>Temp °C</th><th>Humidity</th></tr></thead>
            <tbody>
            <?php foreach ($envLogs as $log): ?>
              <?php
                $temp = (float)$log['temperature'];
                $cls  = $temp < 25 ? 'success' : ($temp <= 30 ? 'warning' : 'danger');
              ?>
              <tr>
                <td><?= htmlspecialchars($log['house_id'] ?? '-') ?></td>
                <td><span class="badge bg-<?= $cls ?>"><?= $temp ?>°C</span></td>
                <td><?= $log['humidity'] ?? '-' ?>%</td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($envLogs)): ?>
              <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <!-- Feed Chart -->
    <div class="col-md-6 advanced-only">
      <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-basket"></i> Feed Consumption (Last 14 Days)</div>
        <div class="card-body"><canvas id="feedChart" height="140"></canvas></div>
      </div>
    </div>
    <!-- Today's Tasks -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-check2-square"></i> Today's Tasks</div>
        <div class="card-body p-0">
          <?php if (empty($todayTasks)): ?>
            <p class="text-center text-muted py-3">All tasks done! 🎉</p>
          <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($todayTasks as $task): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <i class="bi bi-circle text-warning me-1"></i>
                <?= htmlspecialchars(ucfirst(str_replace('_',' ', $task['task_type']))) ?>
              </span>
              <form method="POST" action="/tasks/<?= $task['id'] ?>">
                <input type="hidden" name="status" value="done">
                <button class="btn btn-sm btn-success">✓ Done</button>
              </form>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="/assets/js/app.js"></script>
<script>
const eggData  = <?= $eggChart ?>;
const feedData = <?= $feedChart ?>;

if (document.getElementById('eggChart')) {
  new Chart(document.getElementById('eggChart'), {
    type:'line',
    data:{
      labels: eggData.map(r=>r.date),
      datasets:[{label:'Good Eggs',data:eggData.map(r=>r.total),fill:true,backgroundColor:'rgba(255,193,7,0.15)',borderColor:'#ffc107',tension:0.3}]
    },
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
}
if (document.getElementById('feedChart')) {
  new Chart(document.getElementById('feedChart'), {
    type:'bar',
    data:{
      labels: feedData.map(r=>r.date),
      datasets:[{label:'Feed kg',data:feedData.map(r=>r.total_kg),backgroundColor:'rgba(46,125,50,0.7)'}]
    },
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
}
</script>
</body>
</html>
