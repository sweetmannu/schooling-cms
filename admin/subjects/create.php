<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$categories = $pdo->query("
SELECT id, category_name
FROM categories
WHERE status='Active'
ORDER BY category_name ASC
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $category_id = (int)$_POST['category_id'];
    $subject_name = trim($_POST['subject_name']);
    $slug = strtolower(trim($_POST['slug']));
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if (
        empty($category_id) ||
        empty($subject_name) ||
        empty($slug)
    ) {
        $error = "Please fill all required fields.";
    }

    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM subjects
        WHERE slug=?
        ");

        $check->execute([$slug]);

        if ($check->fetch()) {
            $error = "Slug already exists.";
        }

    }

    if (empty($error)) {

        $insert = $pdo->prepare("
        INSERT INTO subjects
        (
            category_id,
            subject_name,
            slug,
            description,
            status
        )
        VALUES
        (?,?,?,?,?)
        ");

        $insert->execute([
            $category_id,
            $subject_name,
            $slug,
            $description,
            $status
        ]);

        $_SESSION['success']="Subject Added Successfully.";

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

<div class="card-header bg-success text-white">

<h4 class="mb-0">
<i class="fa fa-book"></i>
Add Subject
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

Category

</label>

<select
name="category_id"
class="form-select"
required>

<option value="">Select Category</option>

<?php foreach($categories as $cat){ ?>

<option value="<?= $cat['id']; ?>">

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
name="subject_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Slug

</label>

<input
type="text"
name="slug"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option value="Active">Active</option>

<option value="Inactive">Inactive</option>

</select>

</div>

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Save Subject

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

<?php require_once '../../includes/footer.php'; ?>