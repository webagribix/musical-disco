<?php /** @var array $tasks */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Today's Tasks — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <h4 class="fw-bold mb-3"><i class="bi bi-check2-all"></i> Today's Tasks</h4>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Task Type</th><th>Batch</th><th>Due</th><th>Priority</th><th>Assigned To</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($tasks['data'] as $t): ?>
          <?php $cls = ['high'=>'danger','medium'=>'warning','low'=>'secondary'][$t['priority']] ?? 'secondary'; ?>
          <tr>
            <td><?= ucfirst(str_replace('_',' ', $t['task_type'])) ?></td>
            <td><?= htmlspecialchars($t['batch_name'] ?? $t['batch_id'] ?? '-') ?></td>
            <td><?= $t['due_date'] ?></td>
            <td><span class="badge bg-<?= $cls ?>"><?= $t['priority'] ?></span></td>
            <td><?= htmlspecialchars($t['assigned_to_name'] ?? 'Unassigned') ?></td>
            <td><span class="badge bg-<?= $t['status']==='done' ? 'success' : 'info' ?>"><?= $t['status'] ?></span></td>
            <td><?php if ($t['status'] !== 'done'): ?>
              <form method="POST" action="/tasks/<?= $t['id'] ?>">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="status" value="done">
                <button class="btn btn-sm btn-success"><i class="bi bi-check"></i> Done</button>
              </form>
            <?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tasks['data'])): ?><tr><td colspan="7" class="text-center text-muted py-4">🎉 No tasks due today!</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
