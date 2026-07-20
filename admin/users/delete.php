<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Prevent Self Delete
|--------------------------------------------------------------------------
*/

$currentAdminId =
    isset($_SESSION['admin']['id'])
    ? (int) $_SESSION['admin']['id']
    : 0;

if ($id === $currentAdminId) {
    $_SESSION['error'] = 'You cannot delete your own account.';
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Check User Exists
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, name
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Delete User
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    DELETE FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$_SESSION['success'] = 'User deleted successfully.';

header('Location: index.php');
exit;