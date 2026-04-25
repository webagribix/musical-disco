<?php /** @var array $pl @var string $batchName @var int $batchId */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>P&L Report — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container py-3" style="max-width:820px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold"><i class="bi bi-file-earmark-bar-graph"></i> P&L Report — <?= htmlspecialchars($batchName) ?></h4>
    <a href="/api/v1/reports/batch/<?= $batchId ?>/pdf" class="btn btn-outline-danger btn-sm" target="_blank"><i class="bi bi-file-pdf"></i> Export PDF</a>
  </div>

  <!-- Revenue -->
  <div class="card shadow-sm mb-3 border-success">
    <div class="card-header bg-success text-white fw-semibold">Revenue</div>
    <div class="table-responsive">
      <table class="table mb-0">
        <tbody>
          <tr><td>Egg Sales</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['revenue']['eggs'] ?? 0, 2) ?></td></tr>
          <tr><td>Bird Sales</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['revenue']['birds'] ?? 0, 2) ?></td></tr>
          <tr><td>Manure</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['revenue']['manure'] ?? 0, 2) ?></td></tr>
          <tr><td>Other</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['revenue']['other'] ?? 0, 2) ?></td></tr>
          <tr class="fw-bold table-success"><td>Total Revenue</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['total_revenue'] ?? 0, 2) ?></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Expenses -->
  <div class="card shadow-sm mb-3 border-danger">
    <div class="card-header bg-danger text-white fw-semibold">Expenses</div>
    <div class="table-responsive">
      <table class="table mb-0">
        <tbody>
          <?php foreach ($pl['expenses'] ?? [] as $cat => $amt): ?>
            <tr><td><?= ucfirst($cat) ?></td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($amt, 2) ?></td></tr>
          <?php endforeach; ?>
          <tr class="fw-bold table-danger"><td>Total Expenses</td><td class="text-end"><?= CURRENCY_SYMBOL ?><?= number_format($pl['total_expenses'] ?? 0, 2) ?></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Net -->
  <?php $net = ($pl['net_profit'] ?? 0); $isProfit = $net >= 0; ?>
  <div class="card shadow-sm border-<?= $isProfit ? 'success' : 'danger' ?>">
    <div class="card-body text-center">
      <h5 class="fw-bold">Net <?= $isProfit ? 'Profit' : 'Loss' ?></h5>
      <div class="display-5 fw-bold text-<?= $isProfit ? 'success' : 'danger' ?>"><?= CURRENCY_SYMBOL ?><?= number_format(abs($net), 2) ?></div>
      <?php if (!empty($pl['cost_per_bird'])): ?>
        <div class="mt-2 text-muted">Cost/Bird: <?= CURRENCY_SYMBOL ?><?= number_format($pl['cost_per_bird'],2) ?> &nbsp;|&nbsp; Cost/Egg: <?= CURRENCY_SYMBOL ?><?= number_format($pl['cost_per_egg'] ?? 0,2) ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
