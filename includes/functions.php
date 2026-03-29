<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_post_with_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function sanitize_money(string $raw): float
{
    $normalized = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $raw) ?? '0');
    return max(0, round((float) $normalized, 2));
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function update_expired_withdrawals(PDO $db): void
{
    $stmt = $db->prepare("UPDATE withdrawals SET status = 'rejected', note = CONCAT(COALESCE(note,''), ' Auto-cancelled after 24h.') WHERE status='pending' AND created_at < (NOW() - INTERVAL 24 HOUR)");
    $stmt->execute();
}

function enforce_rate_limit(PDO $db, string $ip, string $email, int $window, int $maxAttempts): bool
{
    $stmt = $db->prepare('SELECT COUNT(*) AS failures FROM login_attempts WHERE ip_address = :ip AND email = :email AND success = 0 AND created_at > (NOW() - INTERVAL :window SECOND)');
    $stmt->bindValue(':ip', $ip);
    $stmt->bindValue(':email', mb_strtolower(trim($email)));
    $stmt->bindValue(':window', $window, PDO::PARAM_INT);
    $stmt->execute();

    $count = (int) ($stmt->fetch()['failures'] ?? 0);
    return $count >= $maxAttempts;
}

function record_login_attempt(PDO $db, string $ip, string $email, bool $success): void
{
    $stmt = $db->prepare('INSERT INTO login_attempts (ip_address, email, success, created_at) VALUES (:ip, :email, :success, NOW())');
    $stmt->execute([
        ':ip' => $ip,
        ':email' => mb_strtolower(trim($email)),
        ':success' => (int) $success,
    ]);
}
