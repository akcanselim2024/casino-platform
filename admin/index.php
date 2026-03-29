<?php
require __DIR__ . '/../includes/bootstrap.php';
$admin = require_admin($db);
$users = (int)$db->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
$pendingWithdrawals = (int)$db->query("SELECT COUNT(*) c FROM withdrawals WHERE status='pending'")->fetch()['c'];
include __DIR__ . '/../includes/views/header.php';
?>
<h1 class="text-2xl font-bold mb-4">Admin Panel</h1>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <div class="bg-zinc-900 p-4 rounded-xl border border-zinc-800">Kullanıcılar: <?= $users ?></div>
    <div class="bg-zinc-900 p-4 rounded-xl border border-zinc-800">Bekleyen Çekim: <?= $pendingWithdrawals ?></div>
</div>
<div class="grid sm:grid-cols-2 gap-4">
    <a class="bg-zinc-900 p-4 rounded-xl border border-zinc-800" href="/admin/users.php">Kullanıcı Yönetimi</a>
    <a class="bg-zinc-900 p-4 rounded-xl border border-zinc-800" href="/admin/finance.php">Finans Yönetimi</a>
    <a class="bg-zinc-900 p-4 rounded-xl border border-zinc-800" href="/admin/bonuses.php">Bonus Yönetimi</a>
    <a class="bg-zinc-900 p-4 rounded-xl border border-zinc-800" href="/admin/settings.php">Site Ayarları</a>
</div>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
