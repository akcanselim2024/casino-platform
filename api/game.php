<?php
require __DIR__ . '/../includes/bootstrap.php';
$user = require_auth($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'message' => 'Method not allowed'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
if (!verify_csrf($payload['csrf_token'] ?? null)) {
    json_response(['ok' => false, 'message' => 'Invalid CSRF token'], 419);
}

$game = (string)($payload['game'] ?? '');
$amount = sanitize_money((string)($payload['amount'] ?? '0'));
if (!in_array($game, ['slot', 'dice', 'crash'], true) || $amount < 1) {
    json_response(['ok' => false, 'message' => 'Geçersiz parametre'], 422);
}

$db->beginTransaction();
try {
    $stmt = $db->prepare('SELECT balance FROM users WHERE id=:uid FOR UPDATE');
    $stmt->execute([':uid' => $user['id']]);
    $balance = (float)($stmt->fetch()['balance'] ?? 0);
    if ($balance < $amount) {
        $db->rollBack();
        json_response(['ok' => false, 'message' => 'Yetersiz bakiye'], 422);
    }

    $multiplier = 0.0;
    $view = '';
    if ($game === 'slot') {
        $symbols = ['🍒', '⭐', '7️⃣', '🍀'];
        $a = $symbols[random_int(0, 3)];
        $b = $symbols[random_int(0, 3)];
        $c = $symbols[random_int(0, 3)];
        $view = "$a $b $c";
        $multiplier = ($a === $b && $b === $c) ? 3.5 : (($a === $b || $b === $c || $a === $c) ? 1.4 : 0);
    } elseif ($game === 'dice') {
        $roll = random_int(1, 6);
        $view = "🎲 $roll";
        $multiplier = $roll >= 4 ? 1.9 : 0;
    } else {
        $crash = round(mt_rand(100, 350) / 100, 2);
        $view = '📈 x' . number_format($crash, 2);
        $multiplier = $crash >= 2.0 ? min($crash, 3.0) : 0;
    }

    $payout = round($amount * $multiplier, 2);
    $delta = $payout - $amount;

    $upd = $db->prepare('UPDATE users SET balance = balance + :delta WHERE id=:uid');
    $upd->execute([':delta' => $delta, ':uid' => $user['id']]);

    $tx = $db->prepare('INSERT INTO transactions (user_id, type, amount, status, description, created_at) VALUES (:uid,:type,:amount,:status,:description,NOW())');
    $tx->execute([':uid' => $user['id'], ':type' => 'bet', ':amount' => $amount, ':status' => $payout > 0 ? 'win' : 'lose', ':description' => "$game result=$view payout=$payout"]);

    $db->commit();
    json_response(['ok' => true, 'result' => $view, 'payout' => $payout]);
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    json_response(['ok' => false, 'message' => 'Sunucu hatası'], 500);
}
