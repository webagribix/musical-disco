<?php
/** @var string $pageTitle */
/** @var array|null $currentUser */
$title      = ($pageTitle ?? 'Dashboard') . ' — ' . APP_NAME;
$featureMode= $currentUser['feature_mode'] ?? 'basic';
$role       = $currentUser['role'] ?? 'worker';
$userId     = $currentUser['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2e7d32">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="feature-<?= htmlspecialchars($featureMode) ?> role-<?= htmlspecialchars($role) ?>">

<?php include __DIR__ . '/nav.php'; ?>

<main class="container-fluid py-3">
  <?php if ($flash = \App\Core\Session::getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flash) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($flash = \App\Core\Session::getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flash) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php echo $content ?? ''; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="/assets/js/app.js"></script>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  }
</script>
</body>
</html>
