<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $wid = (int)($_POST['withdrawal_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    $db->beginTransaction();
    $w = $db->prepare('SELECT * FROM withdrawals WHERE id=:id FOR UPDATE');
    $w->execute([':id' => $wid]);
    $row = $w->fetch();
    if ($row && $row['status'] === 'pending') {
        if ($action === 'approve') {
            $db->prepare("UPDATE withdrawals SET status='approved', processed_at=NOW() WHERE id=:id")->execute([':id' => $wid]);
            $db->prepare('UPDATE users SET balance = balance - :amount WHERE id=:uid')->execute([':amount' => $row['amount'], ':uid' => $row['user_id']]);
            $db->prepare("UPDATE transactions SET status='approved' WHERE user_id=:uid AND type='withdraw' AND status='pending' ORDER BY id DESC LIMIT 1")
                ->execute([':uid' => $row['user_id']]);
        } elseif ($action === 'reject') {
            $db->prepare("UPDATE withdrawals SET status='rejected', processed_at=NOW() WHERE id=:id")->execute([':id' => $wid]);
            $db->prepare("UPDATE transactions SET status='rejected' WHERE user_id=:uid AND type='withdraw' AND status='pending' ORDER BY id DESC LIMIT 1")
                ->execute([':uid' => $row['user_id']]);
        }
    }
    $db->commit();
    flash('success', 'Çekim talebi güncellendi.');
    redirect('/admin/finance.php');
}

$withdrawals = $db->query('SELECT w.*, u.email FROM withdrawals w JOIN users u ON u.id=w.user_id ORDER BY w.id DESC LIMIT 100')->fetchAll();
$transactions = $db->query('SELECT t.*, u.email FROM transactions t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 100')->fetchAll();
include __DIR__ . '/../includes/views/header.php';
?>
<h1 class="text-2xl font-bold mb-3">Finans Yönetimi</h1>
<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 overflow-x-auto mb-4">
    <h2 class="font-semibold mb-2">Çekim Talepleri</h2>
    <table class="w-full min-w-[760px] text-sm"><thead><tr><th>ID</th><th>Email</th><th>Tutar</th><th>IBAN</th><th>Durum</th><th>İşlem</th></tr></thead><tbody>
    <?php foreach ($withdrawals as $w): ?><tr class="border-t border-zinc-800"><td><?= $w['id'] ?></td><td><?= e($w['email']) ?></td><td><?= number_format((float)$w['amount'],2) ?></td><td><?= e($w['iban']) ?></td><td><?= e($w['status']) ?></td><td>
    <?php if ($w['status']==='pending'): ?><form method="post" class="flex gap-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="withdrawal_id" value="<?= $w['id'] ?>"><button name="action" value="approve" class="px-2 py-1 bg-green-600 rounded">Onayla</button><button name="action" value="reject" class="px-2 py-1 bg-red-600 rounded">Reddet</button></form><?php endif; ?>
    </td></tr><?php endforeach; ?>
    </tbody></table>
</div>
<div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 overflow-x-auto">
<h2 class="font-semibold mb-2">Transaction Log</h2>
<table class="w-full min-w-[800px] text-xs"><thead><tr><th>ID</th><th>Email</th><th>Tür</th><th>Tutar</th><th>Durum</th><th>Açıklama</th><th>Tarih</th></tr></thead><tbody>
<?php foreach ($transactions as $tx): ?><tr class="border-t border-zinc-800"><td><?= $tx['id'] ?></td><td><?= e($tx['email']) ?></td><td><?= e($tx['type']) ?></td><td><?= number_format((float)$tx['amount'],2) ?></td><td><?= e($tx['status']) ?></td><td><?= e($tx['description']) ?></td><td><?= e($tx['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
