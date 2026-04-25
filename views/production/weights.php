<?php /** @var array $weights @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Weight Logs — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-speedometer"></i> Weight Logs</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWtModal"><i class="bi bi-plus-circle"></i> Log Weight</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Date</th><th>Batch</th><th>Avg Weight (kg)</th><th>Sample Count</th><th>Notes</th></tr>
        </thead>
        <tbody>
        <?php foreach ($weights['data'] as $w): ?>
          <tr>
            <td><?= $w['weighed_at'] ?></td>
            <td><?= htmlspecialchars($w['batch_name'] ?? $w['batch_id']) ?></td>
            <td><?= number_format($w['average_weight_kg'],3) ?> kg</td>
            <td><?= $w['sample_count'] ?? '-' ?></td>
            <td><?= htmlspecialchars($w['notes'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($weights['data'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No records yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addWtModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Log Weight</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/production/weights">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Date</label><input type="datetime-local" name="weighed_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required></div>
    <div class="row g-2 mb-3">
      <div class="col"><label class="form-label">Avg Weight (kg)</label><input type="number" step="0.001" name="average_weight_kg" class="form-control" required></div>
      <div class="col"><label class="form-label">Sample Count</label><input type="number" name="sample_count" class="form-control" value="10"></div>
    </div>
    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
