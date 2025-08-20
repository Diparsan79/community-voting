<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$issueId = isset($input['issue_id']) ? (int)$input['issue_id'] : 0;
$comment = trim((string)($input['comment'] ?? ''));

if ($issueId <= 0 || $comment === '' || mb_strlen($comment) > 5000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

try {
    $pdo = get_pdo();

    $issueExists = $pdo->prepare('SELECT id FROM issues WHERE id = ?');
    $issueExists->execute([$issueId]);
    if (!$issueExists->fetchColumn()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Issue not found']);
        exit;
    }

    $userId = get_user_identifier();
    $username = is_logged_in() ? current_username() : 'Guest';

    $stmt = $pdo->prepare('INSERT INTO comments (issue_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$issueId, $userId, $comment]);

    echo json_encode([
        'success' => true,
        'username' => $username,
        'comment' => $comment,
        'created_at' => date('M d, Y H:i')
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}