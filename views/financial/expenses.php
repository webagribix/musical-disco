<?php /** @var array $expenses @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Expenses — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-cash-coin"></i> Expenses</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExpModal"><i class="bi bi-plus-circle"></i> Add Expense</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Date</th><th>Batch</th><th>Category</th><th>Amount (<?= CURRENCY_SYMBOL ?>)</th><th>Description</th></tr>
        </thead>
        <tbody>
        <?php foreach ($expenses['data'] as $e): ?>
          <tr>
            <td><?= $e['expense_date'] ?></td>
            <td><?= htmlspecialchars($e['batch_name'] ?? $e['batch_id'] ?? 'Farm') ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($e['category']) ?></span></td>
            <td><?= CURRENCY_SYMBOL ?><?= number_format($e['amount'],2) ?></td>
            <td><?= htmlspecialchars($e['description'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($expenses['data'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No expenses recorded.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="addExpModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add Expense</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/financial/expenses">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch (optional)</label>
      <select name="batch_id" class="form-select">
        <option value="">— Farm-level —</option>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Category</label>
      <select name="category" class="form-select" required>
        <option>feed</option><option>medication</option><option>vaccination</option><option>labor</option><option>utilities</option><option>chicks</option><option>equipment</option><option>other</option>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Amount (<?= CURRENCY_SYMBOL ?>)</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Date</label><input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
    <div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
