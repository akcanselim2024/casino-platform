<?php
$user = current_user($db);
$appName = $siteSettings['site_name'] ?? $config['app']['name'];
$accent = $siteSettings['theme_accent'] ?? '#D4AF37';
?><!doctype html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf" content="<?= e(csrf_token()) ?>">
    <title><?= e($appName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="/assets/js/app.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script>tailwind.config={theme:{extend:{colors:{casinoGold:'<?= e($accent) ?>'}}}}</script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col">
<header class="sticky top-0 z-30 bg-zinc-900/90 backdrop-blur border-b border-zinc-800">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
        <a href="/index.php" class="font-bold text-casinoGold text-lg"><?= e($appName) ?></a>
        <nav class="flex gap-3 text-sm overflow-x-auto">
            <a href="/index.php" class="hover:text-casinoGold">Anasayfa</a>
            <?php if ($user): ?>
                <a href="/profile.php" class="hover:text-casinoGold">Profil</a>
                <a href="/deposit.php" class="hover:text-casinoGold">Yatır</a>
                <a href="/withdraw.php" class="hover:text-casinoGold">Çek</a>
                <?php if ($user['role'] === 'admin'): ?><a href="/admin/index.php" class="hover:text-casinoGold">Admin</a><?php endif; ?>
                <a href="/logout.php" class="text-red-400">Çıkış</a>
            <?php else: ?>
                <a href="/login.php" class="hover:text-casinoGold">Giriş</a>
                <a href="/register.php" class="hover:text-casinoGold">Kayıt</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
<?php foreach (pull_flash() as $f): ?>
<div class="mb-3 rounded-lg border px-4 py-2 <?= $f['type'] === 'error' ? 'border-red-500 text-red-200' : 'border-casinoGold text-zinc-200' ?>">
    <?= e($f['message']) ?>
</div>
<?php endforeach; ?>
