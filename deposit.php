<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_auth($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $amount = sanitize_money((string)($_POST['amount'] ?? '0'));
    if ($amount < 10) {
        flash('error', 'Minimum yatırım 10₺.');
    } else {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('UPDATE users SET balance=balance+:amount WHERE id=:uid');
            $stmt->execute([':amount' => $amount, ':uid' => $user['id']]);

            $tx = $db->prepare('INSERT INTO transactions (user_id,type,amount,status,description,created_at) VALUES (:uid,:type,:amount,:status,:description,NOW())');
            $tx->execute([':uid' => $user['id'], ':type' => 'deposit', ':amount' => $amount, ':status' => 'approved', ':description' => 'Fake payment provider ile onaylandı']);
            $db->commit();
            flash('success', 'Yatırım başarılı ve bakiye güncellendi.');
            redirect('/profile.php');
        } catch (Throwable $e) {
            $db->rollBack();
            flash('error', 'İşlem başarısız.');
        }
    }
}
include __DIR__ . '/includes/views/header.php'; ?>
<form method="post" class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl space-y-4">
    <h1 class="text-2xl font-bold">Yatırım Yap</h1>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input required name="amount" inputmode="decimal" placeholder="Tutar" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <button class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Fake Ödeme ile Yatır</button>
</form>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
