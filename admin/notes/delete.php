<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

/* Get Note */

$stmt = $pdo->prepare("
SELECT pdf_file, thumbnail
FROM notes
WHERE id=?
");

$stmt->execute([$id]);

$note = $stmt->fetch();

if (!$note) {

    $_SESSION['error'] = "Note not found.";

    header("Location: index.php");
    exit;

}

/* Delete PDF */

if (!empty($note['pdf_file'])) {

    $pdfPath = "../../uploads/pdf/" . $note['pdf_file'];

    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }

}

/* Delete Thumbnail */

if (!empty($note['thumbnail'])) {

    $thumbPath = "../../uploads/thumbnails/" . $note['thumbnail'];

    if (file_exists($thumbPath)) {
        unlink($thumbPath);
    }

}

/* Delete Database Record */

$delete = $pdo->prepare("
DELETE FROM notes
WHERE id=?
");

$delete->execute([$id]);

$_SESSION['success'] = "Note Deleted Successfully.";

header("Location: index.php");
exit;