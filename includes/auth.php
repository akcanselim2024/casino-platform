<?php

declare(strict_types=1);

function bootstrap_session(array $config): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name($config['app']['session_name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - (int) $_SESSION['last_activity']) > (int) $config['app']['inactivity_timeout']) {
        session_unset();
        session_destroy();
        session_start();
        flash('warning', 'Oturum süresi doldu. Lütfen tekrar giriş yapın.');
    }

    $_SESSION['last_activity'] = $now;
}

function current_user(PDO $db): ?array
{
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }

    $stmt = $db->prepare('SELECT id, name, email, role, balance, is_banned, iban, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => (int) $id]);
    $user = $stmt->fetch();

    if (!$user || (int) $user['is_banned'] === 1) {
        logout();
        return null;
    }

    return $user;
}

function login(PDO $db, string $email, string $password, string $ip, array $config): bool
{
    if (enforce_rate_limit($db, $ip, $email, $config['security']['rate_limit_window'], $config['security']['rate_limit_attempts'])) {
        flash('error', 'Çok fazla başarısız giriş denemesi. Lütfen 5 dakika sonra tekrar deneyin.');
        return false;
    }

    $stmt = $db->prepare('SELECT id, password, is_banned FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => mb_strtolower(trim($email))]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password']) || (int) $user['is_banned'] === 1) {
        record_login_attempt($db, $ip, $email, false);
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    record_login_attempt($db, $ip, $email, true);

    return true;
}

function register_user(PDO $db, string $name, string $email, string $password): bool
{
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => mb_strtolower(trim($email))]);
    if ($stmt->fetch()) {
        return false;
    }

    $insert = $db->prepare('INSERT INTO users (name, email, password, role, balance, created_at) VALUES (:name, :email, :password, :role, :balance, NOW())');
    return $insert->execute([
        ':name' => trim($name),
        ':email' => mb_strtolower(trim($email)),
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => 'user',
        ':balance' => 0,
    ]);
}

function require_auth(PDO $db): array
{
    $user = current_user($db);
    if (!$user) {
        flash('error', 'Bu alana erişmek için giriş yapın.');
        redirect('/login.php');
    }

    return $user;
}

function require_admin(PDO $db): array
{
    $user = require_auth($db);
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }

    return $user;
}

function logout(): never
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    redirect('/login.php');
}
