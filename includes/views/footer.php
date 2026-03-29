</main>
<footer class="sticky bottom-0 bg-zinc-900 border-t border-zinc-800 text-xs text-zinc-400 p-3 text-center">
    © <?= date('Y') ?> <?= e($siteSettings['site_name'] ?? $config['app']['name']) ?>
</footer>
<?php if (!empty($siteSettings['live_support_script'])): ?>
<div id="live-support-container"><?= $siteSettings['live_support_script'] ?></div>
<?php endif; ?>
<div id="toast-container" class="fixed bottom-20 right-4 space-y-2 z-50"></div>
</body>
</html>
