<?php /** @var array $vaccinations @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Vaccinations — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-capsule"></i> Vaccinations</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVaccModal"><i class="bi bi-plus-circle"></i> Log Vaccination</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Batch</th><th>Vaccine</th><th>Date</th><th>Route</th><th>By</th></tr>
        </thead>
        <tbody>
        <?php foreach ($vaccinations['data'] as $v): ?>
          <tr>
            <td><?= htmlspecialchars($v['batch_name'] ?? $v['batch_id']) ?></td>
            <td><?= htmlspecialchars($v['vaccine_name']) ?></td>
            <td><?= $v['administered_at'] ?></td>
            <td><?= $v['route'] ?></td>
            <td><?= htmlspecialchars($v['administered_by'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($vaccinations['data'])): ?><tr><td colspan="5" class="text-center text-muted py-4">No records yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addVaccModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Log Vaccination</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/health/vaccinations">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Batch</label>
      <select name="batch_id" class="form-select" required>
        <?php foreach ($batches as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3"><label class="form-label">Vaccine Name</label><input type="text" name="vaccine_name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Date & Time</label><input type="datetime-local" name="administered_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required></div>
    <div class="mb-3"><label class="form-label">Route</label>
      <select name="route" class="form-select"><option>drinking_water</option><option>spray</option><option>eye_drop</option><option>injection</option><option>oral</option></select>
    </div>
    <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
