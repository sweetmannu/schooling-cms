<?php

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../config/config.php';

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$dsn = 'mysql:host='
    . DB_HOST
    . ';dbname='
    . DB_NAME
    . ';charset='
    . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_STRINGIFY_FETCHES  => false
];

try {

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        $options
    );

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Development Environment
    |--------------------------------------------------------------------------
    |
    | Localhost par actual error debugging ke liye visible rahega.
    |
    */

    if (
        defined('APP_ENV') &&
        APP_ENV === 'development'
    ) {

        die(
            'Database Connection Failed: '
            . htmlspecialchars(
                $e->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            )
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Production Environment
    |--------------------------------------------------------------------------
    |
    | User ko sensitive database details nahi dikhengi.
    | Actual error server error log me save hoga.
    |
    */

    error_log(
        'Database connection failed: '
        . $e->getMessage()
    );

    http_response_code(500);

    die(
        'A database connection error occurred. '
        . 'Please try again later.'
    );

}