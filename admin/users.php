<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $id = (int)($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'balance') {
        $amount = sanitize_money((string)($_POST['amount'] ?? '0'));
        $db->prepare('UPDATE users SET balance=:amount WHERE id=:id')->execute([':amount' => $amount, ':id' => $id]);
    } elseif ($action === 'toggle_ban') {
        $db->prepare('UPDATE users SET is_banned = 1 - is_banned WHERE id=:id')->execute([':id' => $id]);
    }
    flash('success', 'Kullanıcı güncellendi.');
    redirect('/admin/users.php');
}
$users = $db->query('SELECT id,name,email,role,balance,is_banned,created_at FROM users ORDER BY id DESC LIMIT 200')->fetchAll();
include __DIR__ . '/../includes/views/header.php';
?>
<h1 class="text-2xl font-bold mb-3">Kullanıcı Yönetimi</h1>
<div class="overflow-x-auto bg-zinc-900 border border-zinc-800 rounded-xl">
<table class="w-full min-w-[820px] text-sm"><thead><tr class="text-left text-zinc-400"><th>ID</th><th>Ad</th><th>Email</th><th>Rol</th><th>Bakiye</th><th>Durum</th><th>İşlem</th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
<tr class="border-t border-zinc-800"><td><?= $u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= number_format((float)$u['balance'],2) ?></td><td><?= (int)$u['is_banned'] ? 'Banned':'Active' ?></td><td>
<form method="post" class="flex gap-2">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
<input name="amount" placeholder="Yeni bakiye" class="px-2 py-1 bg-zinc-800 border border-zinc-700 rounded w-28">
<button name="action" value="balance" class="px-2 py-1 bg-casinoGold text-black rounded">Bakiye</button>
<button name="action" value="toggle_ban" class="px-2 py-1 bg-red-500 text-white rounded">Ban/Aç</button>
</form>
</td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
