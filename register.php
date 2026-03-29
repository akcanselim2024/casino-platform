<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_with_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($name) < 2 || strlen($password) < 8) {
        flash('error', 'Ad en az 2 karakter ve şifre en az 8 karakter olmalı.');
    } elseif (!register_user($db, $name, $email, $password)) {
        flash('error', 'Bu e-posta zaten kayıtlı.');
    } else {
        flash('success', 'Kayıt başarılı, giriş yapabilirsiniz.');
        redirect('/login.php');
    }
}
include __DIR__ . '/includes/views/header.php';
?>
<form method="post" class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl space-y-4">
    <h1 class="text-2xl font-bold">Kayıt Ol</h1>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input required name="name" placeholder="Ad Soyad" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input required name="email" type="email" placeholder="E-posta" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <input required name="password" type="password" placeholder="Şifre" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700">
    <button class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Kayıt</button>
</form>
<?php include __DIR__ . '/includes/views/footer.php'; ?>
