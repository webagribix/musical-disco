<?php /** @var array $inventory */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Feed Inventory — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-box-seam"></i> Feed Inventory</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addInvModal"><i class="bi bi-plus-circle"></i> Add Stock</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Feed Type</th><th>Supplier</th><th>Qty (kg)</th><th>Cost/kg</th><th>Purchase Date</th><th>Expiry</th></tr>
        </thead>
        <tbody>
        <?php foreach ($inventory['data'] as $i): ?>
          <?php $low = $i['quantity_kg'] < ($i['reorder_level'] ?? 50); ?>
          <tr class="<?= $low ? 'table-warning' : '' ?>">
            <td><?= htmlspecialchars($i['feed_type']) ?><?= $low ? ' <span class="badge bg-warning text-dark">Low</span>' : '' ?></td>
            <td><?= htmlspecialchars($i['supplier'] ?? '-') ?></td>
            <td><?= number_format($i['quantity_kg'],2) ?> kg</td>
            <td><?= CURRENCY_SYMBOL ?><?= number_format($i['cost_per_kg'],2) ?></td>
            <td><?= $i['purchase_date'] ?></td>
            <td><?= $i['expiry_date'] ?? '-' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($inventory['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No inventory records.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addInvModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add Feed Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/feed/inventory">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Feed Type</label><input type="text" name="feed_type" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Quantity (kg)</label><input type="number" step="0.01" name="quantity_kg" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Cost per kg (<?= CURRENCY_SYMBOL ?>)</label><input type="number" step="0.01" name="cost_per_kg" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Purchase Date</label><input type="date" name="purchase_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
