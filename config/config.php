<?php

/*
|--------------------------------------------------------------------------
| Application Environment
|--------------------------------------------------------------------------
|
| development = localhost par error screen par dikhenge
| production  = live server par errors user ko nahi dikhenge
|
|--------------------------------------------------------------------------
*/

define('APP_ENV', 'development');

/*
|--------------------------------------------------------------------------
| Application Settings
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'Schooling Education');

define(
    'APP_URL',
    APP_ENV === 'production'
        ? 'https://yourdomain.com'
        : 'http://localhost/schooling-cms'
);

/*
|--------------------------------------------------------------------------
| Database Settings
|--------------------------------------------------------------------------
*/

define(
    'DB_HOST',
    APP_ENV === 'production'
        ? 'localhost'
        : 'localhost'
);

define(
    'DB_NAME',
    APP_ENV === 'production'
        ? 'live_database_name'
        : 'schooling_cms'
);

define(
    'DB_USER',
    APP_ENV === 'production'
        ? 'live_database_user'
        : 'root'
);

define(
    'DB_PASS',
    APP_ENV === 'production'
        ? 'live_database_password'
        : ''
);

define('DB_CHARSET', 'utf8mb4');

/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Kolkata');

/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
*/

if (APP_ENV === 'development') {

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    error_reporting(E_ALL);

} else {

    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');

    ini_set('log_errors', '1');

    error_reporting(E_ALL);

}

/*
|--------------------------------------------------------------------------
| Secure Session Configuration
|--------------------------------------------------------------------------
|
| Session settings session_start() se pehle apply honi chahiye.
|
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    $isHttps = (
        isset($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== '' &&
        strtolower((string) $_SERVER['HTTPS']) !== 'off'
    );

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

}

/*
|--------------------------------------------------------------------------
| Session Fixation Protection
|--------------------------------------------------------------------------
|
| Login ke samay bhi session_regenerate_id(true) use karna zaroori hai.
|
|--------------------------------------------------------------------------
*/