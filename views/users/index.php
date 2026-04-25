<?php /** @var array $users */ ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><title>Users — <?= APP_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head><body>
<?php include __DIR__ . '/../layout/nav.php'; ?>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between mb-3">
    <h4 class="fw-bold"><i class="bi bi-people"></i> Users</h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-person-plus"></i> Add User</button>
  </div>
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-success">
          <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users['data'] as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
            <td><span class="badge bg-<?= ['owner'=>'dark','manager'=>'primary','worker'=>'secondary'][$u['role']] ?? 'secondary' ?>"><?= $u['role'] ?></span></td>
            <td><span class="badge bg-<?= $u['deleted_at'] ? 'danger' : 'success' ?>"><?= $u['deleted_at'] ? 'Inactive' : 'Active' ?></span></td>
            <td>
              <?php if (!$u['deleted_at'] && $u['id'] !== ($_SESSION['user_id'] ?? null)): ?>
              <form method="POST" action="/users/<?= $u['id'] ?>/deactivate" class="d-inline">
                <button class="btn btn-sm btn-outline-danger">Deactivate</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($users['data'])): ?><tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form method="POST" action="/users">
  <div class="modal-body">
    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Role</label>
      <select name="role" class="form-select"><option value="worker">Worker</option><option value="manager">Manager</option><option value="owner">Owner</option></select>
    </div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Create User</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
