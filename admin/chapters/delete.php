<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$check = $pdo->prepare("SELECT id FROM chapters WHERE id=?");
$check->execute([$id]);

if (!$check->fetch()) {
    $_SESSION['error'] = "Chapter not found.";
    header("Location: index.php");
    exit;
}

$delete = $pdo->prepare("DELETE FROM chapters WHERE id=?");
$delete->execute([$id]);

$_SESSION['success'] = "Chapter Deleted Successfully.";

header("Location: index.php");
exit;