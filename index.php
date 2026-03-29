<?php require __DIR__ . '/includes/bootstrap.php'; ?>
<?php include __DIR__ . '/includes/views/header.php'; ?>
<?php $user = current_user($db); ?>
<section class="rounded-2xl bg-gradient-to-r from-zinc-900 to-yellow-900 p-6 mb-6">
    <h1 class="text-3xl font-bold mb-2">5.000₺ Bonus ile Oyna</h1>
    <p class="text-zinc-200">Modern, hızlı ve güvenli bahis deneyimi.</p>
    <?php if ($user): ?><p class="mt-4">Bakiyeniz: <strong class="text-casinoGold"><?= number_format((float)$user['balance'],2) ?>₺</strong></p><?php endif; ?>
</section>
<section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ([['slot','Slot Çarkı'],['dice','Dice'],['crash','Crash']] as [$slug,$label]): ?>
    <a href="/games/<?= $slug ?>.php" class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 hover:border-casinoGold">
        <h3 class="font-semibold text-casinoGold"><?= e($label) ?></h3>
        <p class="text-sm text-zinc-400">Bahis yap ve anında sonucu gör.</p>
    </a>
    <?php endforeach; ?>
</section>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
