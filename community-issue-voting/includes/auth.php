<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_register(string $username, string $email, string $password): array {
    $pdo = get_pdo();

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [false, 'Invalid email address'];
    }

    if (strlen($username) < 3) {
        return [false, 'Username must be at least 3 characters'];
    }

    if (strlen($password) < 6) {
        return [false, 'Password must be at least 6 characters'];
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        return [false, 'Email or username already in use'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$username, $email, $hash]);

    $userId = (int)$pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;

    return [true, null];
}

function auth_login(string $email, string $password): array {
    $pdo = get_pdo();

    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return [false, 'Invalid credentials'];
    }

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];

    return [true, null];
}

function auth_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_username(): string {
    return isset($_SESSION['username']) ? (string)$_SESSION['username'] : 'Guest';
}

function is_logged_in(): bool {
    return current_user_id() !== null;
}