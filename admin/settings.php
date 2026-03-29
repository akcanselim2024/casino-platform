<?php
require __DIR__ . '/../includes/bootstrap.php';
require_admin($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $allowed = ['site_name', 'logo_url', 'theme_accent', 'live_support_script'];
    foreach ($allowed as $key) {
        $value = trim((string)($_POST[$key] ?? ''));
        $stmt = $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=:v2');
        $stmt->execute([':k' => $key, ':v' => $value, ':v2' => $value]);
    }
    flash('success', 'Ayarlar kaydedildi.');
    redirect('/admin/settings.php');
}
include __DIR__ . '/../includes/views/header.php';
?>
<h1 class="text-2xl font-bold mb-3">Site Ayarları</h1>
<form method="post" class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 space-y-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input name="site_name" value="<?= e($siteSettings['site_name'] ?? '') ?>" placeholder="Site adı" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input name="logo_url" value="<?= e($siteSettings['logo_url'] ?? '') ?>" placeholder="Logo URL" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input name="theme_accent" value="<?= e($siteSettings['theme_accent'] ?? '#D4AF37') ?>" placeholder="#D4AF37" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <textarea name="live_support_script" rows="5" placeholder="Tawk.to script" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700"><?= e($siteSettings['live_support_script'] ?? '') ?></textarea>
    <button class="p-3 rounded bg-casinoGold text-black font-semibold">Kaydet</button>
</form>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
