<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = get_pdo();
$sort = $_GET['sort'] ?? 'recent';
$orderSql = $sort === 'top' ? 'score DESC, i.created_at DESC' : 'i.created_at DESC';

$sql = "
SELECT i.id, i.title, i.description, i.image, i.created_at,
       COALESCE(SUM(CASE WHEN v.vote_type = 1 THEN 1 WHEN v.vote_type = -1 THEN -1 ELSE 0 END), 0) AS score
FROM issues i
LEFT JOIN votes v ON v.issue_id = i.id
GROUP BY i.id
ORDER BY $orderSql
LIMIT 100
";
$issues = $pdo->query($sql)->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<script>window.BASE_URL = '<?php echo e(BASE_URL); ?>';</script>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">Issues</h1>
  <div>
    <a class="btn btn-sm <?php echo $sort==='recent'?'btn-primary':'btn-outline-primary'; ?>" href="?sort=recent">Most Recent</a>
    <a class="btn btn-sm <?php echo $sort==='top'?'btn-primary':'btn-outline-primary'; ?>" href="?sort=top">Most Upvoted</a>
  </div>
</div>

<div class="row g-3">
<?php foreach ($issues as $issue): ?>
  <div class="col-12">
    <div class="card issue-card" data-issue-card="<?php echo (int)$issue['id']; ?>">
      <div class="card-body d-flex">
        <div class="vote-box text-center me-3">
          <button class="vote-btn" data-vote="up" data-issue-id="<?php echo (int)$issue['id']; ?>" title="Upvote">▲</button>
          <div class="vote-count" data-vote-count><?php echo (int)$issue['score']; ?></div>
          <button class="vote-btn" data-vote="down" data-issue-id="<?php echo (int)$issue['id']; ?>" title="Downvote">▼</button>
        </div>
        <div class="flex-grow-1">
          <h2 class="h5 mb-1"><a href="issue.php?id=<?php echo (int)$issue['id']; ?>" class="text-decoration-none"><?php echo e($issue['title']); ?></a></h2>
          <p class="text-muted mb-1 small">Posted <?php echo e(format_datetime($issue['created_at'])); ?></p>
          <p class="mb-0"><?php echo e(truncate_text($issue['description'])); ?></p>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>