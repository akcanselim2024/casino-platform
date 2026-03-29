<?php
require __DIR__ . '/../includes/bootstrap.php';
$user = require_auth($db);
include __DIR__ . '/../includes/views/header.php'; ?>
<section x-data="gameClient('crash')" class="max-w-xl mx-auto bg-zinc-900 border border-zinc-800 p-6 rounded-xl">
    <h1 class="text-2xl font-bold text-casinoGold mb-2">Crash</h1>
    <div class="text-4xl text-center my-6" x-text="result || '📈 x1.00'"></div>
    <input x-model="amount" inputmode="decimal" class="w-full p-3 rounded bg-zinc-800 border border-zinc-700 mb-3" placeholder="Bahis tutarı">
    <button @click="play" class="w-full p-3 rounded bg-casinoGold text-black font-semibold">Başlat</button>
</section>
<?php include __DIR__ . '/../includes/views/footer.php'; ?>
