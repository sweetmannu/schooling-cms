<?php

if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}

function setting($key, $default = '')
{
    global $pdo;

    static $settings = null;

    if ($settings === null) {

        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settings) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}