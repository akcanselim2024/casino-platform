<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $token = bin2hex(random_bytes(24));
    $stmt = $db->prepare('UPDATE users SET reset_token=:token, reset_expires_at=(NOW()+INTERVAL 1 HOUR) WHERE email=:email');
    $stmt->execute([':token' => $token, ':email' => mb_strtolower(trim($email))]);
    flash('success', 'Demo mod: sıfırlama tokenı loglara yazıldı.');
    error_log('Reset token for ' . $email . ': ' . $token);
}
include __DIR__ . '/includes/views/header.php'; ?>
<form method="post" class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl space-y-4">
    <h1 class="text-2xl font-bold">Şifre Sıfırlama</h1>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input required name="email" type="email" placeholder="E-posta" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <button class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Sıfırlama İsteği Gönder</button>
</form>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
