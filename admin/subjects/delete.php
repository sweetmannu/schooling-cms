<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Check Subject Exists
$check = $pdo->prepare("
SELECT id
FROM subjects
WHERE id=?
");

$check->execute([$id]);

if (!$check->fetch()) {
    $_SESSION['error'] = "Subject not found.";
    header("Location: index.php");
    exit;
}

// Delete Subject
$delete = $pdo->prepare("
DELETE FROM subjects
WHERE id=?
");

$delete->execute([$id]);

$_SESSION['success'] = "Subject Deleted Successfully.";

header("Location: index.php");
exit;   