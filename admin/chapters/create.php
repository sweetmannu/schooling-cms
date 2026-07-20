<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';

$subjects = $pdo->query("
SELECT id, subject_name
FROM subjects
WHERE status='Active'
ORDER BY subject_name ASC
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $subject_id = isset($_POST['subject_id'])
    ? (int)$_POST['subject_id']
    : 0;

$chapter_name = trim(strip_tags($_POST['chapter_name'] ?? ''));

$slug = strtolower(trim($_POST['slug'] ?? ''));
$slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
$slug = trim($slug, '-');

$description = trim(strip_tags($_POST['description'] ?? ''));

$chapter_order = isset($_POST['chapter_order'])
    ? (int)$_POST['chapter_order']
    : 1;
    if ($chapter_order < 1) {
    $chapter_order = 1;
}

$status = (($_POST['status'] ?? 'Active') === 'Inactive')
    ? 'Inactive'
    : 'Active';

    if (
        empty($subject_id) ||
        empty($chapter_name) ||
        empty($slug)
    ) {

        $error = "Please fill all required fields.";

    }

    /* Duplicate Chapter Check */

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM chapters
    WHERE chapter_name=?
    ");

    $check->execute([$chapter_name]);

    if ($check->fetch()) {

        $error = "Chapter already exists.";

    }

}

    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM chapters
        WHERE slug=?
        ");

        $check->execute([$slug]);

        if ($check->fetch()) {

            $error = "Slug already exists.";

        }

    }

    if (empty($error)) {

        $insert = $pdo->prepare("
        INSERT INTO chapters
        (
            subject_id,
            chapter_name,
            slug,
            description,
            chapter_order,
            status
        )
        VALUES
        (?,?,?,?,?,?)
        ");

        $insert->execute([
            $subject_id,
            $chapter_name,
            $slug,
            $description,
            $chapter_order,
            $status
        ]);

        $_SESSION['success'] = "Chapter Added Successfully.";

        header("Location: index.php");
        exit;

    }

}

require_once '../../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">
<i class="fa fa-book"></i>
Add Chapter
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

Subject

</label>

<select
name="subject_id"
class="form-select"
required>

<option value="">Select Subject</option>

<?php foreach($subjects as $subject){ ?>

<option
value="<?= $subject['id']; ?>"
<?= (($_POST['subject_id'] ?? '') == $subject['id']) ? 'selected' : ''; ?>>

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
id="chapter_name"
name="chapter_name"
class="form-control"
value="<?= htmlspecialchars($_POST['chapter_name'] ?? ''); ?>"
required>

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
value="<?= htmlspecialchars($_POST['slug'] ?? ''); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"><?= htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Chapter Order

</label>

<input
type="number"
name="chapter_order"
class="form-control"
value="<?= htmlspecialchars($_POST['chapter_order'] ?? '1'); ?>"
min="1"
required>

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
<?= (($_POST['status'] ?? 'Active') == 'Active') ? 'selected' : ''; ?>>
Active
</option>

<option
value="Inactive"
<?= (($_POST['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>
Inactive
</option>

</select>

</div>

<button
type="submit"
class="btn btn-primary">

<i class="fa fa-save"></i>

Save Chapter

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

const chapterInput = document.getElementById('chapter_name');

if (chapterInput) {

    chapterInput.addEventListener('input', function () {

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