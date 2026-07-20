<?php

require_once __DIR__ . '/../config/config.php';

/*
|--------------------------------------------------------------------------
| Admin Entry Point
|--------------------------------------------------------------------------
|
| Agar admin login hai to dashboard par bhejo,
| warna login page par.
|
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;