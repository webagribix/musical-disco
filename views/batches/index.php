<?php /** @var array $batches */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Batches — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold"><i class="bi bi-collection"></i> Batches</h4>
    <a href="/batches/create" class="btn btn-success"><i class="bi bi-plus-circle"></i> New Batch</a>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Name</th><th>Stage</th><th>Initial Count</th><th>Placement</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($batches['data'] as $b): ?>
          <tr>
            <td><a href="/batches/<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></a></td>
            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($b['stage']) ?></span></td>
            <td><?= number_format($b['initial_count']) ?></td>
            <td><?= $b['placement_date'] ?></td>
            <td><span class="badge bg-<?= $b['status']==='active' ? 'success' : 'secondary' ?>"><?= $b['status'] ?></span></td>
            <td>
              <a href="/batches/<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
              <a href="/batches/<?= $b['id'] ?>/edit" class="btn btn-sm btn-outline-secondary">Edit</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($batches['data'])): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No batches yet. <a href="/batches/create">Create one</a>.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
