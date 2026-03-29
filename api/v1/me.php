<?php
require __DIR__ . '/../../includes/bootstrap.php';
$user = require_auth($db);
json_response([
    'id' => (int)$user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'balance' => (float)$user['balance'],
    'role' => $user['role'],
]);
