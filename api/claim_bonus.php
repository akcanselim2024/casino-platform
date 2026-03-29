<?php
require __DIR__ . '/../includes/bootstrap.php';
$user = require_auth($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false], 405);
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
if (!verify_csrf($payload['csrf_token'] ?? null)) {
    json_response(['ok' => false, 'message' => 'CSRF'], 419);
}
$code = (string)($payload['code'] ?? 'daily');

$stmt = $db->prepare('SELECT * FROM bonuses WHERE code=:code AND is_active=1 LIMIT 1');
$stmt->execute([':code' => $code]);
$bonus = $stmt->fetch();
if (!$bonus) {
    json_response(['ok' => false, 'message' => 'Bonus bulunamadı'], 404);
}

$claim = $db->prepare('SELECT created_at FROM bonus_history WHERE user_id=:uid AND bonus_code=:code ORDER BY id DESC LIMIT 1');
$claim->execute([':uid' => $user['id'], ':code' => $code]);
$last = $claim->fetch();
if ($last && $code === 'daily' && strtotime($last['created_at']) > time() - 86400) {
    json_response(['ok' => false, 'message' => 'Günlük bonus zaten alındı'], 422);
}
if ($last && $code === 'welcome') {
    json_response(['ok' => false, 'message' => 'Hoş geldin bonusu tek seferliktir'], 422);
}

$amount = (float)$bonus['type'] === 'percent' ? round((float)$user['balance'] * ((float)$bonus['value'] / 100), 2) : (float)$bonus['value'];
$wager = $amount * (float)$bonus['wager_multiplier'];

$db->beginTransaction();
$u = $db->prepare('UPDATE users SET balance=balance+:amount, wager_remaining=wager_remaining+:wager WHERE id=:uid');
$u->execute([':amount' => $amount, ':wager' => $wager, ':uid' => $user['id']]);
$b = $db->prepare('INSERT INTO bonus_history (user_id, bonus_code, bonus_name, bonus_amount, wager_required, created_at) VALUES (:uid,:code,:name,:amount,:wager,NOW())');
$b->execute([':uid' => $user['id'], ':code' => $code, ':name' => $bonus['name'], ':amount' => $amount, ':wager' => $wager]);
$t = $db->prepare('INSERT INTO transactions (user_id, type, amount, status, description, created_at) VALUES (:uid,\'bonus\',:amount,\'approved\',:d,NOW())');
$t->execute([':uid' => $user['id'], ':amount' => $amount, ':d' => $bonus['name']]);
$db->commit();

json_response(['ok' => true, 'message' => 'Bonus tanımlandı', 'amount' => $amount]);
