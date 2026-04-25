<?php /** @var array $medications @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Medications — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-hospital"></i> Medications</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMedModal"><i class="bi bi-plus-circle"></i> Log Medication</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Batch</th><th>Drug</th><th>Dose</th><th>Start</th><th>End</th><th>Reason</th></tr>
        </thead>
        <tbody>
        <?php foreach ($medications['data'] as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['batch_name'] ?? $m['batch_id']) ?></td>
            <td><?= htmlspecialchars($m['drug_name']) ?></td>
            <td><?= $m['dosage'] ?> <?= $m['unit'] ?></td>
            <td><?= $m['start_date'] ?></td>
            <td><?= $m['end_date'] ?></td>
            <td><?= htmlspecialchars($m['reason'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($medications['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No records yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addMedModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Log Medication</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/health/medications">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Drug Name</label><input type="text" name="drug_name" class="form-control" required></div>
    <div class="row g-2 mb-3">
      <div class="col"><label class="form-label">Dosage</label><input type="number" step="0.01" name="dosage" class="form-control"></div>
      <div class="col"><label class="form-label">Unit</label><input type="text" name="unit" class="form-control" value="ml"></div>
    </div>
    <div class="row g-2 mb-3">
      <div class="col"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
      <div class="col"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control"></div>
    </div>
    <div class="mb-3"><label class="form-label">Reason</label><input type="text" name="reason" class="form-control"></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
