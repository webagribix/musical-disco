<?php
/** @var array $batch @var int $liveCount @var float $fcr @var array $transfers @var array $vaccSchedule @var string|null $qrCode */
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title><?= htmlspecialchars($batch['batch_name']) ?> — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-collection"></i> <?= htmlspecialchars($batch['batch_name']) ?></h4>
    <div>
      <a href="/batches/<?= $batch['id'] ?>/edit" class="btn btn-outline-secondary btn-sm">Edit</a>
    </div>
  </div>

  <!-- Stage Timeline -->
  <?php
  $stages  = ['brooding','growing','point_of_lay','market_ready'];
  $current = array_search($batch['stage'], $stages);
  ?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex gap-2 flex-wrap">
        <?php foreach ($stages as $i => $stage): ?>
          <span class="badge rounded-pill <?= $i <= $current ? 'bg-success' : 'bg-light text-dark border' ?>" style="font-size:0.9rem;padding:0.5rem 1rem">
            <?= ucfirst(str_replace('_',' ',$stage)) ?>
          </span>
          <?php if ($i < count($stages)-1): ?>
            <span class="text-muted align-self-center">→</span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="display-6 fw-bold text-success"><?= number_format($liveCount) ?></div><small>Live Count</small></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="display-6 fw-bold"><?= $batch['initial_count'] ?></div><small>Initial Count</small></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="display-6 fw-bold text-info"><?= $fcr ?></div><small>FCR</small></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body">
      <?php $mortality = $batch['initial_count'] - $liveCount; ?>
      <div class="display-6 fw-bold text-danger"><?= $mortality ?></div><small>Total Mortality</small>
    </div></div></div>
  </div>

  <div class="row g-3">
    <!-- Transfer History -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Transfer History</div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead><tr><th>Date</th><th>From</th><th>To</th><th>Count</th></tr></thead>
            <tbody>
            <?php foreach ($transfers as $t): ?>
              <tr><td><?= $t['transferred_at'] ?></td><td><?= $t['from_house_id'] ?></td><td><?= $t['to_house_id'] ?></td><td><?= $t['count'] ?></td></tr>
            <?php endforeach; ?>
            <?php if (empty($transfers)): ?><tr><td colspan="4" class="text-center text-muted">No transfers</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Pending Vaccinations -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Pending Vaccinations</div>
        <ul class="list-group list-group-flush">
          <?php foreach ($vaccSchedule as $v): ?>
            <li class="list-group-item"><strong><?= htmlspecialchars($v['vaccine_name']) ?></strong> — <?= $v['due_date'] ?></li>
          <?php endforeach; ?>
          <?php if (empty($vaccSchedule)): ?><li class="list-group-item text-muted">None pending</li><?php endif; ?>
        </ul>
      </div>
    </div>
    <!-- QR Code -->
    <?php if ($qrCode): ?>
    <div class="col-md-2">
      <div class="card shadow-sm text-center">
        <div class="card-header fw-semibold">House QR</div>
        <div class="card-body"><img src="<?= $qrCode ?>" class="img-fluid" alt="QR Code"></div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
