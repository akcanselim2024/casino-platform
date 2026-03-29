function toast(message, type = 'info') {
  const el = document.createElement('div');
  el.className = `px-4 py-2 rounded shadow text-sm ${type === 'error' ? 'bg-red-600' : 'bg-zinc-800 border border-yellow-500'}`;
  el.textContent = message;
  document.getElementById('toast-container')?.appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

function gameClient(game) {
  return {
    amount: '',
    result: '',
    async play() {
      const csrf = document.querySelector('meta[name=csrf]')?.content || window.CSRF_TOKEN;
      const res = await fetch('/api/game.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({game, amount: this.amount, csrf_token: csrf}),
      });
      const json = await res.json();
      if (!json.ok) return toast(json.message || 'İşlem başarısız', 'error');
      this.result = `${json.result} | Ödeme: ${Number(json.payout).toFixed(2)}₺`;
      toast('Bahis tamamlandı');
    }
  }
}
window.gameClient = gameClient;
window.toast = toast;
