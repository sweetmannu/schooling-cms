<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

verify_csrf_token($_POST['csrf_token'] ?? '');

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

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

    if (is_file($pdfPath)) {
    if (!unlink($pdfPath)) {
        // Optional: log error
    }
}

}

/* Delete Thumbnail */

if (!empty($note['thumbnail'])) {

    $thumbPath = "../../uploads/thumbnails/" . $note['thumbnail'];

    if (is_file($thumbPath)) {
    if (!unlink($thumbPath)) {
        // Optional: log error
    }
}

}

/* Delete Database Record */

$delete = $pdo->prepare("
DELETE FROM notes
WHERE id=?
");

$delete->execute([$id]);

if ($delete->rowCount()) {
    $_SESSION['success'] = "Note Deleted Successfully.";
} else {
    $_SESSION['error'] = "Unable to delete note.";
}

header("Location: index.php");
exit;