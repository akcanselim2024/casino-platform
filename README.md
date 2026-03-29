# RoyalGold Casino Platform

## Kurulum
1. MySQL'de `casino_platform` veritabanını oluşturun.
2. `database.sql` dosyasını içe aktarın.
3. `includes/config.php` içinde veritabanı ayarlarını düzenleyin.
4. PHP 8.1+ ile proje kökünü serve edin.

## Varsayılan admin
- Email: `admin@casino.local`
- Şifre: `Admin123!`

## Özellikler
- PHP OOP + PDO + modüler yapı
- TailwindCSS dark+gold responsive tema
- Kullanıcı kayıt/giriş/şifre sıfırlama
- Deposit/withdraw/bonus/bet transaction sistemi
- Slot, Dice, Crash oyunları (AJAX)
- CSRF, XSS kaçışları, prepared statements, rate limit, inactivity timeout
- Admin panel: kullanıcı, finans, bonus, ayar yönetimi
- API altyapısı: `/api/v1/me.php`
