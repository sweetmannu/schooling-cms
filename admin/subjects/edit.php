<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id=?");
$stmt->execute([$id]);
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    header("Location: index.php");
    exit;
}

$categories = $pdo->query("
SELECT id, category_name
FROM categories
WHERE status='Active'
ORDER BY category_name ASC
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $category_id = isset($_POST['category_id'])
    ? (int)$_POST['category_id']
    : 0;
    $subject_name = trim(strip_tags($_POST['subject_name'] ?? ''));

$slug = strtolower(trim($_POST['slug'] ?? ''));
$slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
$slug = trim($slug, '-');

$description = trim(strip_tags($_POST['description'] ?? ''));

$status = (($_POST['status'] ?? 'Active') === 'Inactive')
    ? 'Inactive'
    : 'Active';

    if (
        empty($category_id) ||
        empty($subject_name) ||
        empty($slug)
    ) {
        $error = "Please fill all required fields.";
    }

    /* Duplicate Subject Check */

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM subjects
    WHERE subject_name=?
    AND id!=?
    ");

    $check->execute([
        $subject_name,
        $id
    ]);

    if ($check->fetch()) {

        $error = "Subject already exists.";

    }

}

    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM subjects
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
        UPDATE subjects
        SET
            category_id=?,
            subject_name=?,
            slug=?,
            description=?,
            status=?,
            updated_at=NOW()
        WHERE id=?
        ");

        $update->execute([
            $category_id,
            $subject_name,
            $slug,
            $description,
            $status,
            $id
        ]);

        $_SESSION['success'] = "Subject Updated Successfully.";

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
Edit Subject
</h4>

</div>

<div class="card-body">

<?php if(!empty($error)){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<form method="POST" autocomplete="off">

<input
type="hidden"
name="csrf_token"
value="<?= csrf_token(); ?>">

<div class="mb-3">

<label class="form-label">

Category

</label>

<select
name="category_id"
class="form-select"
required>

<?php foreach($categories as $cat){ ?>

<option
value="<?= $cat['id']; ?>"

<?= (($_POST['category_id'] ?? $subject['category_id']) == $cat['id']) ? 'selected' : ''; ?>>

<?= htmlspecialchars($cat['category_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Subject Name

</label>

<input
type="text"
id="subject_name"
name="subject_name"
class="form-control"
required
value="<?= htmlspecialchars($_POST['subject_name'] ?? $subject['subject_name']); ?>">

</div>

<div class="mb-3">

<label class="form-label">


Slug

</label>

<input
type="text"
id="slug"
name="slug"
class="form-control"
required
value="<?= htmlspecialchars($_POST['slug'] ?? $subject['slug']); ?>">

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"><?= htmlspecialchars($_POST['description'] ?? $subject['description']); ?></textarea>

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
<?= (($_POST['status'] ?? $subject['status']) == 'Active') ? 'selected' : ''; ?>>

Active

</option>

<option
value="Inactive"
<?= (($_POST['status'] ?? $subject['status']) == 'Inactive') ? 'selected' : ''; ?>>

Inactive

</option>

</select>

</div>

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Update Subject

</button>

<a
href="index.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</div>

<script>

const subjectInput = document.getElementById('subject_name');

if (subjectInput) {

    subjectInput.addEventListener('input', function () {

        let slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        document.getElementById('slug').value = slug;

    });

}

</script>

<?php require_once '../../includes/footer.php'; ?>