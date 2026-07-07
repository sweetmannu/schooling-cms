<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

/* Current Chapter */

$stmt = $pdo->prepare("
SELECT *
FROM chapters
WHERE id=?
");

$stmt->execute([$id]);

$chapter = $stmt->fetch();

if (!$chapter) {
    header("Location: index.php");
    exit;
}

/* Subjects */

$subjects = $pdo->query("
SELECT id, subject_name
FROM subjects
WHERE status='Active'
ORDER BY subject_name ASC
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $subject_id = (int)$_POST['subject_id'];
    $chapter_name = trim($_POST['chapter_name']);
    $slug = strtolower(trim($_POST['slug']));
    $description = trim($_POST['description']);
    $chapter_order = (int)$_POST['chapter_order'];
    $status = $_POST['status'];

    if (
        empty($subject_id) ||
        empty($chapter_name) ||
        empty($slug)
    ) {
        $error = "Please fill all required fields.";
    }

    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM chapters
        WHERE slug=?
        AND id!=?
        ");

        $check->execute([$slug, $id]);

        if ($check->fetch()) {
            $error = "Slug already exists.";
        }

    }

    if (empty($error)) {

        $update = $pdo->prepare("
        UPDATE chapters
        SET
            subject_id=?,
            chapter_name=?,
            slug=?,
            description=?,
            chapter_order=?,
            status=?,
            updated_at=NOW()
        WHERE id=?
        ");

        $update->execute([
            $subject_id,
            $chapter_name,
            $slug,
            $description,
            $chapter_order,
            $status,
            $id
        ]);

        $_SESSION['success'] = "Chapter Updated Successfully.";

        header("Location: index.php");
        exit;

    }

}

require_once '../../includes/header.php';

?><div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-warning text-dark">

<h4 class="mb-0">
<i class="fa fa-edit"></i>
Edit Chapter
</h4>

</div>

<div class="card-body">

<?php if(!empty($error)){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Subject

</label>

<select
name="subject_id"
class="form-select"
required>

<?php foreach($subjects as $subject){ ?>

<option
value="<?= $subject['id']; ?>"

<?= ($chapter['subject_id']==$subject['id']) ? "selected" : ""; ?>>

<?= htmlspecialchars($subject['subject_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Chapter Name

</label>

<input
type="text"
name="chapter_name"
class="form-control"
required
value="<?= htmlspecialchars($chapter['chapter_name']); ?>">

</div>

<div class="mb-3">

<label class="form-label">

Slug

</label>

<input
type="text"
name="slug"
class="form-control"
required
value="<?= htmlspecialchars($chapter['slug']); ?>">

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"><?= htmlspecialchars($chapter['description']); ?></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Chapter Order

</label>

<input
type="number"
name="chapter_order"
class="form-control"
required
value="<?= $chapter['chapter_order']; ?>">

</div>

<div class="mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option
value="Active"
<?= ($chapter['status']=="Active") ? "selected" : ""; ?>>

Active

</option>

<option
value="Inactive"
<?= ($chapter['status']=="Inactive") ? "selected" : ""; ?>>

Inactive

</option>

</select>

</div>

<div class="mt-4">

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Update Chapter

</button>

<a
href="index.php"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>