<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['app']['timezone']);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

bootstrap_session($config);
$db = DB::conn($config['db']);
update_expired_withdrawals($db);

$settingsStmt = $db->query('SELECT setting_key, setting_value FROM site_settings');
$siteSettings = [];
foreach ($settingsStmt->fetchAll() as $row) {
    $siteSettings[$row['setting_key']] = $row['setting_value'];
}
