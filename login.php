<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    if (login($db, $email, $password, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', $config)) {
        flash('success', 'Hoş geldiniz!');
        redirect('/profile.php');
    }
    flash('error', 'E-posta veya şifre hatalı.');
}
include __DIR__ . '/includes/views/header.php';
?>
<form method="post" class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl space-y-4">
    <h1 class="text-2xl font-bold">Giriş Yap</h1>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input required name="email" type="email" placeholder="E-posta" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input required name="password" type="password" placeholder="Şifre" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <button class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Giriş</button>
    <a class="text-sm text-casinoGold" href="/reset-password.php">Şifremi unuttum</a>
</form>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
