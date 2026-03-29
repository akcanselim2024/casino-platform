<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_auth($db);

$transactions = $db->prepare('SELECT type, amount, status, description, created_at FROM transactions WHERE user_id=:uid ORDER BY id DESC LIMIT 50');
$transactions->execute([':uid' => $user['id']]);
$bonuses = $db->prepare('SELECT bonus_name, bonus_amount, wager_required, created_at FROM bonus_history WHERE user_id=:uid ORDER BY id DESC LIMIT 20');
$bonuses->execute([':uid' => $user['id']]);

include __DIR__ . '/includes/views/header.php';
?>
<div class="grid lg:grid-cols-3 gap-4">
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4">
        <h2 class="text-xl font-bold">Hesap</h2>
        <p><?= e($user['name']) ?></p>
        <p class="text-zinc-400"><?= e($user['email']) ?></p>
        <p class="mt-2">Bakiye: <span class="text-casinoGold font-semibold"><?= number_format((float)$user['balance'],2) ?>₺</span></p>
    </div>
    <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-xl p-4 overflow-x-auto">
        <h3 class="font-semibold mb-2">İşlem Geçmişi</h3>
        <table class="w-full text-sm min-w-[640px]"><thead><tr class="text-left text-zinc-400"><th>Tür</th><th>Tutar</th><th>Durum</th><th>Açıklama</th><th>Tarih</th></tr></thead><tbody>
        <?php foreach ($transactions as $tx): ?><tr class="border-t border-zinc-800"><td><?= e($tx['type']) ?></td><td><?= number_format((float)$tx['amount'],2) ?></td><td><?= e($tx['status']) ?></td><td><?= e($tx['description']) ?></td><td><?= e($tx['created_at']) ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div>
</div>
<div class="mt-4 bg-zinc-900 border border-zinc-800 rounded-xl p-4 overflow-x-auto">
    <h3 class="font-semibold mb-2">Bonus Geçmişi</h3>
    <table class="w-full text-sm min-w-[520px]"><thead><tr class="text-left text-zinc-400"><th>Bonus</th><th>Tutar</th><th>Çevrim</th><th>Tarih</th></tr></thead><tbody>
    <?php foreach ($bonuses as $b): ?><tr class="border-t border-zinc-800"><td><?= e($b['bonus_name']) ?></td><td><?= number_format((float)$b['bonus_amount'],2) ?></td><td><?= number_format((float)$b['wager_required'],2) ?></td><td><?= e($b['created_at']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
</div>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
