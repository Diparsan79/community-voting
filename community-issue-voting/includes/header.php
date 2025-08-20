<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(BASE_URL); ?>/assets/css/style.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="<?php echo e(BASE_URL); ?>/"><?php echo e(APP_NAME); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="<?php echo e(BASE_URL); ?>/">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(BASE_URL); ?>/add_issue.php">Add Issue</a></li>
          </ul>
          <div class="d-flex">
            <?php if (is_logged_in()): ?>
              <span class="navbar-text me-3">Hi, <?php echo e(current_username()); ?></span>
              <a class="btn btn-outline-light btn-sm" href="<?php echo e(BASE_URL); ?>/logout.php">Logout</a>
            <?php else: ?>
              <a class="btn btn-outline-light btn-sm me-2" href="<?php echo e(BASE_URL); ?>/login.php">Login</a>
              <a class="btn btn-warning btn-sm" href="<?php echo e(BASE_URL); ?>/register.php">Register</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>
    <main class="container my-4">