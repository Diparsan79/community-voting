<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = get_pdo();
$errors = [];
$title = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imagePath = null;

    if ($title === '' || mb_strlen($title) < 3) {
        $errors[] = 'Title must be at least 3 characters.';
    }
    if ($description === '' || mb_strlen($description) < 10) {
        $errors[] = 'Description must be at least 10 characters.';
    }

    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > MAX_UPLOAD_BYTES) {
                $errors[] = 'Image is too large.';
            } else {
                $mime = mime_content_type($file['tmp_name']);
                if (!in_array($mime, ALLOWED_IMAGE_MIME, true)) {
                    $errors[] = 'Invalid image format.';
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $basename = bin2hex(random_bytes(8)) . '.' . $ext;
                    $dest = UPLOAD_DIR . $basename;
                    if (!move_uploaded_file($file['tmp_name'], $dest)) {
                        $errors[] = 'Failed to save image.';
                    } else {
                        $imagePath = $basename;
                    }
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Image upload error.';
        }
    }

    if (!$errors) {
        $creator = get_user_identifier();
        $stmt = $pdo->prepare('INSERT INTO issues (title, description, image, created_by, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $imagePath, $creator]);
        redirect('/');
    }
}

include __DIR__ . '/includes/header.php';
?>
<script>window.BASE_URL = '<?php echo e(BASE_URL); ?>';</script>
<h1 class="h3 mb-3">Add Issue</h1>
<?php if ($errors): ?>
<div class="alert alert-danger">
  <ul class="mb-0">
  <?php foreach ($errors as $e): ?><li><?php echo e($e); ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card p-3">
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" class="form-control" name="title" value="<?php echo e($title); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="6" required><?php echo e($description); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Image (optional)</label>
    <input type="file" class="form-control" name="image" accept="image/*">
  </div>
  <button class="btn btn-primary">Submit</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>