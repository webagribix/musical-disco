<?php
/** @var string|null $error */
/** @var bool $register */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
<div class="container" style="max-width:420px">
  <div class="card shadow-sm mt-5">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <span style="font-size:2.5rem">🐔</span>
        <h3 class="fw-bold text-success"><?= APP_NAME ?></h3>
        <p class="text-muted small">Farm OS for Africa</p>
      </div>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST" action="/login">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Login</button>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
