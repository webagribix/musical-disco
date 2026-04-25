<?php /** @var array $logs @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Feed Consumption — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-basket"></i> Feed Consumption</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFeedModal"><i class="bi bi-plus-circle"></i> Log Feed</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Date</th><th>Batch</th><th>Type</th><th>Qty (kg)</th><th>Notes</th></tr>
        </thead>
        <tbody>
        <?php foreach ($logs['data'] as $r): ?>
          <tr>
            <td><?= $r['consumed_date'] ?></td>
            <td><?= htmlspecialchars($r['batch_name'] ?? $r['batch_id']) ?></td>
            <td><?= htmlspecialchars($r['feed_type'] ?? '-') ?></td>
            <td><?= number_format($r['quantity_kg'],2) ?> kg</td>
            <td><?= htmlspecialchars($r['notes'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($logs['data'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No records yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addFeedModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Log Feed Consumption</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/feed">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Date</label><input type="date" name="consumed_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
    <div class="mb-3"><label class="form-label">Quantity (kg)</label><input type="number" step="0.01" name="quantity_kg" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Feed Type</label><input type="text" name="feed_type" class="form-control" placeholder="Starter, Grower, Finisher..."></div>
    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
