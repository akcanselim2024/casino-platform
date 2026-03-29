<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'RoyalGold Casino',
        'url' => 'http://localhost',
        'timezone' => 'UTC',
        'session_name' => 'royalgold_session',
        'inactivity_timeout' => 1800,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'casino_platform',
        'charset' => 'utf8mb4',
        'user' => 'root',
        'pass' => '',
    ],
    'security' => [
        'csrf_key' => 'change-this-long-random-secret',
        'rate_limit_attempts' => 6,
        'rate_limit_window' => 300,
    ],
];
