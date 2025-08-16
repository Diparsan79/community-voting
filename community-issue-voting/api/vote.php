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
$vote = $input['vote_type'] ?? '';
$voteValue = $vote === 'up' ? 1 : ($vote === 'down' ? -1 : 0);

if ($issueId <= 0 || $voteValue === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

try {
    $pdo = get_pdo();
    $userId = get_user_identifier();

    $pdo->beginTransaction();

    $existsStmt = $pdo->prepare('SELECT id, vote_type FROM votes WHERE issue_id = ? AND user_id = ? FOR UPDATE');
    $existsStmt->execute([$issueId, $userId]);
    $existing = $existsStmt->fetch();

    if ($existing) {
        if ((int)$existing['vote_type'] === $voteValue) {
            // Undo vote if same button clicked (toggle off)
            $del = $pdo->prepare('DELETE FROM votes WHERE id = ?');
            $del->execute([$existing['id']]);
        } else {
            $upd = $pdo->prepare('UPDATE votes SET vote_type = ?, created_at = NOW() WHERE id = ?');
            $upd->execute([$voteValue, $existing['id']]);
        }
    } else {
        $ins = $pdo->prepare('INSERT INTO votes (issue_id, user_id, vote_type, created_at) VALUES (?, ?, ?, NOW())');
        $ins->execute([$issueId, $userId, $voteValue]);
    }

    $scoreStmt = $pdo->prepare('SELECT COALESCE(SUM(CASE WHEN vote_type = 1 THEN 1 WHEN vote_type = -1 THEN -1 ELSE 0 END), 0) AS score FROM votes WHERE issue_id = ?');
    $scoreStmt->execute([$issueId]);
    $score = (int)$scoreStmt->fetchColumn();

    $pdo->commit();

    echo json_encode(['success' => true, 'score' => $score]);
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}