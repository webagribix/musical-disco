<?php /** @var array $breeds @var array $houses @var array|null $batch @var bool $edit */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title><?= isset($edit) ? 'Edit' : 'New' ?> Batch — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container py-4" style="max-width:600px">
  <h4 class="fw-bold mb-3"><?= isset($edit) ? 'Edit Batch' : 'New Batch' ?></h4>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="<?= isset($edit) ? '/batches/'.$batch['id'] : '/batches' ?>">
        <div class="mb-3">
          <label class="form-label">Batch Name *</label>
          <input type="text" name="batch_name" class="form-control" value="<?= htmlspecialchars($batch['batch_name'] ?? '') ?>" required>
        </div>
        <div class="row g-3 mb-3">
          <div class="col">
            <label class="form-label">Breed</label>
            <select name="breed_id" class="form-select">
              <option value="">— Select —</option>
              <?php foreach ($breeds as $br): ?>
                <option value="<?= $br['id'] ?>" <?= ($batch['breed_id'] ?? '') == $br['id'] ? 'selected' : '' ?>><?= htmlspecialchars($br['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col">
            <label class="form-label">House *</label>
            <select name="house_id" class="form-select" required>
              <option value="">— Select —</option>
              <?php foreach ($houses as $h): ?>
                <option value="<?= $h['id'] ?>" <?= ($batch['house_id'] ?? '') == $h['id'] ? 'selected' : '' ?>><?= htmlspecialchars($h['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col">
            <label class="form-label">Initial Count *</label>
            <input type="number" name="initial_count" class="form-control" value="<?= $batch['initial_count'] ?? '' ?>" min="1" required>
          </div>
          <div class="col">
            <label class="form-label">Placement Date *</label>
            <input type="date" name="placement_date" class="form-control" value="<?= $batch['placement_date'] ?? date('Y-m-d') ?>" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Source / Hatchery</label>
          <input type="text" name="source" class="form-control" value="<?= htmlspecialchars($batch['source'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($batch['notes'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-success">Save</button>
          <a href="/batches" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
