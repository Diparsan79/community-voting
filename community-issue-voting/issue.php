<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = get_pdo();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) redirect('/');

$issueStmt = $pdo->prepare("SELECT i.*, COALESCE(SUM(CASE WHEN v.vote_type = 1 THEN 1 WHEN v.vote_type = -1 THEN -1 ELSE 0 END), 0) AS score
  FROM issues i LEFT JOIN votes v ON v.issue_id = i.id WHERE i.id = ? GROUP BY i.id LIMIT 1");
$issueStmt->execute([$id]);
$issue = $issueStmt->fetch();
if (!$issue) redirect('/');

$commentsStmt = $pdo->prepare('SELECT c.*, u.username FROM comments c LEFT JOIN users u ON u.id = CASE WHEN c.user_id > 0 THEN c.user_id ELSE NULL END WHERE c.issue_id = ? ORDER BY c.created_at DESC LIMIT 200');
$commentsStmt->execute([$id]);
$comments = $commentsStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<script>window.BASE_URL = '<?php echo e(BASE_URL); ?>';</script>
<div class="card mb-3" data-issue-card="<?php echo (int)$issue['id']; ?>">
  <div class="card-body d-flex">
    <div class="vote-box text-center me-3">
      <button class="vote-btn" data-vote="up" data-issue-id="<?php echo (int)$issue['id']; ?>" title="Upvote">▲</button>
      <div class="vote-count" data-vote-count><?php echo (int)$issue['score']; ?></div>
      <button class="vote-btn" data-vote="down" data-issue-id="<?php echo (int)$issue['id']; ?>" title="Downvote">▼</button>
    </div>
    <div class="flex-grow-1">
      <h1 class="h4 mb-1"><?php echo e($issue['title']); ?></h1>
      <p class="text-muted small mb-2">Posted <?php echo e(format_datetime($issue['created_at'])); ?></p>
      <?php if (!empty($issue['image'])): ?>
        <img class="img-fluid rounded mb-3" src="<?php echo e(UPLOAD_BASE_URL . $issue['image']); ?>" alt="Issue image">
      <?php endif; ?>
      <div><?php echo nl2br(e($issue['description'])); ?></div>
    </div>
  </div>
</div>

<h2 class="h5">Comments</h2>
<div id="comments-list" class="card p-3 mb-3">
  <?php foreach ($comments as $c): ?>
    <div class="comment">
      <div class="meta"><?php echo e($c['username'] ? $c['username'] : 'Guest'); ?> · <?php echo e(format_datetime($c['created_at'])); ?></div>
      <div class="text"><?php echo nl2br(e($c['comment'])); ?></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card p-3">
  <form id="comment-form">
    <input type="hidden" name="issue_id" value="<?php echo (int)$issue['id']; ?>">
    <div class="mb-2">
      <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts..." required></textarea>
    </div>
    <div class="text-end">
      <button class="btn btn-primary">Post Comment</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>