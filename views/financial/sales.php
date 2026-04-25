<?php /** @var array $sales @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Sales — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-bag-check"></i> Sales</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSaleModal"><i class="bi bi-plus-circle"></i> Record Sale</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Date</th><th>Batch</th><th>Type</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Buyer</th></tr>
        </thead>
        <tbody>
        <?php foreach ($sales['data'] as $s): ?>
          <tr>
            <td><?= $s['sale_date'] ?></td>
            <td><?= htmlspecialchars($s['batch_name'] ?? $s['batch_id']) ?></td>
            <td><span class="badge bg-info text-dark"><?= $s['sale_type'] ?></span></td>
            <td><?= number_format($s['quantity']) ?></td>
            <td><?= CURRENCY_SYMBOL ?><?= number_format($s['unit_price'],2) ?></td>
            <td><?= CURRENCY_SYMBOL ?><?= number_format($s['quantity'] * $s['unit_price'],2) ?></td>
            <td><?= htmlspecialchars($s['buyer_name'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($sales['data'])): ?><tr><td colspan="7" class="text-center text-muted py-4">No sales recorded.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addSaleModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Record Sale</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/financial/sales">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Sale Type</label>
      <select name="sale_type" class="form-select" required>
        <option value="eggs">Eggs</option><option value="birds">Live Birds</option><option value="manure">Manure</option><option value="other">Other</option>
      </select>
    </div>
    <div class="row g-2 mb-3">
      <div class="col"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" min="1" required></div>
      <div class="col"><label class="form-label">Unit Price (<?= CURRENCY_SYMBOL ?>)</label><input type="number" step="0.01" name="unit_price" class="form-control" required></div>
    </div>
    <div class="mb-3"><label class="form-label">Date</label><input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
    <div class="mb-3"><label class="form-label">Buyer Name</label><input type="text" name="buyer_name" class="form-control"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
