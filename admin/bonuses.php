<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $db->prepare('DELETE FROM bonuses WHERE id=:id')->execute([':id' => (int)$_POST['id']]);
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            ':code' => trim((string)$_POST['code']),
            ':name' => trim((string)$_POST['name']),
            ':type' => ($_POST['type'] ?? 'fixed') === 'percent' ? 'percent' : 'fixed',
            ':value' => sanitize_money((string)$_POST['value']),
            ':wager' => max(0, (float)($_POST['wager_multiplier'] ?? 1)),
            ':active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($id > 0) {
            $db->prepare('UPDATE bonuses SET code=:code,name=:name,type=:type,value=:value,wager_multiplier=:wager,is_active=:active WHERE id=:id')->execute($data + [':id' => $id]);
        } else {
            $db->prepare('INSERT INTO bonuses (code,name,type,value,wager_multiplier,is_active,created_at) VALUES (:code,:name,:type,:value,:wager,:active,NOW())')->execute($data);
        }
    }
    flash('success', 'Bonus ayarlandı.');
    redirect('/admin/bonuses.php');
}
$bonuses = $db->query('SELECT * FROM bonuses ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../includes/views/header.php';
?>
<h1 class="text-2xl font-bold mb-3">Bonus Yönetimi</h1>
<form method="post" class="grid md:grid-cols-6 gap-2 bg-zinc-900 border border-zinc-800 p-4 rounded-xl mb-4">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<input name="code" placeholder="code" class="p-2 rounded bg-zinc-800 border border-zinc-700">
<input name="name" placeholder="Bonus Adı" class="p-2 rounded bg-zinc-800 border border-zinc-700">
<select name="type" class="p-2 rounded bg-zinc-800 border border-zinc-700"><option value="fixed">Sabit</option><option value="percent">Yüzde</option></select>
<input name="value" placeholder="Değer" class="p-2 rounded bg-zinc-800 border border-zinc-700">
<input name="wager_multiplier" placeholder="Çevrim x" class="p-2 rounded bg-zinc-800 border border-zinc-700" value="1">
<label class="p-2"><input type="checkbox" checked name="is_active"> Aktif</label>
<button class="md:col-span-6 p-2 rounded bg-casinoGold text-black">Bonus Ekle</button>
</form>
<div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-x-auto"><table class="w-full min-w-[700px] text-sm"><thead><tr><th>ID</th><th>Code</th><th>Ad</th><th>Tip</th><th>Değer</th><th>Çevrim</th><th>Aktif</th><th>Sil</th></tr></thead><tbody>
<?php foreach ($bonuses as $b): ?><tr class="border-t border-zinc-800"><td><?= $b['id'] ?></td><td><?= e($b['code']) ?></td><td><?= e($b['name']) ?></td><td><?= e($b['type']) ?></td><td><?= $b['value'] ?></td><td><?= $b['wager_multiplier'] ?></td><td><?= $b['is_active'] ?></td><td><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button class="text-red-400" name="action" value="delete">Sil</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
