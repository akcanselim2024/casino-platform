<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_auth($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $amount = sanitize_money((string)($_POST['amount'] ?? '0'));
    $iban = trim((string)($_POST['iban'] ?? ''));

    if ($amount < 50 || strlen($iban) < 10) {
        flash('error', 'Minimum çekim 50₺ ve geçerli IBAN gerekli.');
    } elseif ($amount > (float)$user['balance']) {
        flash('error', 'Yetersiz bakiye.');
    } else {
        $stmt = $db->prepare('INSERT INTO withdrawals (user_id, amount, iban, status, created_at) VALUES (:uid,:amount,:iban,:status,NOW())');
        $stmt->execute([':uid' => $user['id'], ':amount' => $amount, ':iban' => $iban, ':status' => 'pending']);

        $tx = $db->prepare('INSERT INTO transactions (user_id,type,amount,status,description,created_at) VALUES (:uid,:type,:amount,:status,:description,NOW())');
        $tx->execute([':uid' => $user['id'], ':type' => 'withdraw', ':amount' => $amount, ':status' => 'pending', ':description' => 'IBAN çekim talebi']);
        flash('success', 'Çekim talebi oluşturuldu. 24 saat içinde işlenmezse otomatik iptal edilir.');
        redirect('/profile.php');
    }
}
include __DIR__ . '/includes/views/header.php'; ?>
<form method="post" class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl space-y-4">
    <h1 class="text-2xl font-bold">Para Çekme</h1>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input required name="amount" inputmode="decimal" placeholder="Tutar" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input required name="iban" placeholder="TR.. IBAN" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <button class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Çekim Talebi Gönder</button>
</form>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
