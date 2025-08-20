<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$error = null;
$username = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    [$ok, $err] = auth_register($username, $email, $password);
    if ($ok) {
        redirect('/');
    } else {
        $error = $err;
    }
}

include __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Register</h1>
<?php if ($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
<form method="post" class="card p-3">
  <div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" class="form-control" name="username" value="<?php echo e($username); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" name="email" value="<?php echo e($email); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" name="password" required>
  </div>
  <button class="btn btn-primary">Create account</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>