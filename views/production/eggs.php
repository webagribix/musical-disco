<?php /** @var array $collections @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Egg Collection — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-egg-fried"></i> Egg Collection</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEggModal"><i class="bi bi-plus-circle"></i> Log Collection</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-warning">
          <tr><th>Date</th><th>Batch</th><th>Good Eggs</th><th>Damaged</th><th>Undersized</th><th>Recorded By</th></tr>
        </thead>
        <tbody>
        <?php foreach ($collections['data'] as $c): ?>
          <tr>
            <td><?= $c['collection_date'] ?></td>
            <td><?= htmlspecialchars($c['batch_name'] ?? $c['batch_id']) ?></td>
            <td><?= number_format($c['good_eggs']) ?></td>
            <td><?= number_format($c['damaged_eggs'] ?? 0) ?></td>
            <td><?= number_format($c['undersized_eggs'] ?? 0) ?></td>
            <td><?= htmlspecialchars($c['recorded_by'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($collections['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No records yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addEggModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Log Egg Collection</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/production/eggs">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Date</label><input type="date" name="collection_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
    <div class="row g-2 mb-3">
      <div class="col"><label class="form-label">Good Eggs</label><input type="number" name="good_eggs" class="form-control" min="0" required></div>
      <div class="col"><label class="form-label">Damaged</label><input type="number" name="damaged_eggs" class="form-control" min="0" value="0"></div>
    </div>
    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
