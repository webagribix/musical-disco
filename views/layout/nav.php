<?php
/** @var array|null $currentUser */
$role        = $currentUser['role']         ?? 'worker';
$featureMode = $currentUser['feature_mode'] ?? 'basic';
$pendingTasks= $currentUser ? \App\Models\Task::pendingCount((int)$currentUser['id']) : 0;
$activeAlerts= \App\Models\Alert::activeCount();
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#2e7d32;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/dashboard">
      <i class="bi bi-egg-fried"></i> <?= APP_NAME ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php if ($currentUser): ?>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/batches"><i class="bi bi-collection"></i> Batches</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-heart-pulse"></i> Health</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/health/vaccinations">Vaccinations</a></li>
            <li><a class="dropdown-item" href="/health/medications">Medications</a></li>
            <li><a class="dropdown-item" href="/health/alerts">Alerts</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="/feed"><i class="bi bi-basket"></i> Feed</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-egg"></i> Production</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/production/eggs">Eggs</a></li>
            <li><a class="dropdown-item" href="/production/weights">Weights</a></li>
          </ul>
        </li>
        <?php if (in_array($role, ['owner','manager'], true)): ?>
        <li class="nav-item dropdown <?= $featureMode === 'basic' ? 'd-none' : '' ?>">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-cash-stack"></i> Financial</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/financial/expenses">Expenses</a></li>
            <li><a class="dropdown-item" href="/financial/sales">Sales</a></li>
            <li><a class="dropdown-item" href="/financial/pl">P&amp;L Report</a></li>
          </ul>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link position-relative" href="/tasks">
            <i class="bi bi-check2-square"></i> Tasks
            <?php if ($pendingTasks > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"><?= $pendingTasks ?></span>
            <?php endif; ?>
          </a>
        </li>
        <?php if ($role === 'owner'): ?>
        <li class="nav-item"><a class="nav-link" href="/users"><i class="bi bi-people"></i> Users</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link position-relative" href="/health/alerts">
            <i class="bi bi-bell"></i>
            <?php if ($activeAlerts > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $activeAlerts ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($currentUser['name']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars(ucfirst($role)) ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="/logout" class="dropdown-item p-0">
                <input type="hidden" name="_method" value="POST">
                <button type="submit" class="btn btn-link text-danger dropdown-item">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</nav>
