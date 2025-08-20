<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
    if (str_starts_with($path, 'http')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    }
    exit;
}

function ensure_guest_id(): int {
    if (is_logged_in()) {
        return current_user_id() ?? 0;
    }
    if (isset($_COOKIE['guest_id'])) {
        return (int)$_COOKIE['guest_id'];
    }
    $random = random_int(100000000, 999999999);
    $guestId = -$random; // negative range for guests
    setcookie('guest_id', (string)$guestId, [
        'expires' => time() + 60 * 60 * 24 * 365, // 1 year
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    return $guestId;
}

function get_user_identifier(): int {
    return is_logged_in() ? (current_user_id() ?? 0) : ensure_guest_id();
}

function format_datetime(string $ts): string {
    return date('M d, Y H:i', strtotime($ts));
}

function truncate_text(string $text, int $max = 180): string {
    $trimmed = trim($text);
    if (mb_strlen($trimmed) <= $max) return $trimmed;
    return mb_substr($trimmed, 0, $max - 1) . '…';
}