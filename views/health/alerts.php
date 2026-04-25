<?php /** @var array $alerts */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Alerts — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <h4 class="fw-bold mb-3"><i class="bi bi-bell-fill text-warning"></i> Farm Alerts</h4>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-warning">
          <tr><th>Type</th><th>Batch</th><th>Message</th><th>Created</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($alerts['data'] as $a): ?>
          <?php $severity = $a['severity'] ?? 'info'; $cls = ['critical'=>'danger','warning'=>'warning','info'=>'info'][$severity] ?? 'secondary'; ?>
          <tr>
            <td><span class="badge bg-<?= $cls ?>"><?= htmlspecialchars($a['alert_type']) ?></span></td>
            <td><?= htmlspecialchars($a['batch_name'] ?? $a['batch_id'] ?? '-') ?></td>
            <td><?= htmlspecialchars($a['message']) ?></td>
            <td><?= $a['created_at'] ?></td>
            <td><?= $a['is_resolved'] ? '<span class="badge bg-success">Resolved</span>' : '<span class="badge bg-warning text-dark">Active</span>' ?></td>
            <td><?php if (!$a['is_resolved']): ?>
              <form method="POST" action="/alerts/<?= $a['id'] ?>/resolve"><button class="btn btn-sm btn-success">Resolve</button></form>
            <?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($alerts['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No alerts.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
