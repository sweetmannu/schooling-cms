<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

/* Current Category */

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$id]);

$category = $stmt->fetch();

if (!$category) {
    header("Location: index.php");
    exit;
}

/* Parent Categories */

$parentCategories = $pdo->prepare("
SELECT id, category_name
FROM categories
WHERE id != ?
AND parent_id IS NULL
ORDER BY category_name ASC
");

$parentCategories->execute([$id]);

$parents = $parentCategories->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $parent_id = !empty($_POST['parent_id'])
        ? (int) $_POST['parent_id']
        : NULL;

    $name = trim($_POST['category_name']);
    $slug = strtolower(trim($_POST['slug']));
    $status = $_POST['status'];

    if ($name == "" || $slug == "") {
        $error = "All fields are required.";
    }

    /* Duplicate Slug Check */

    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM categories
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
        UPDATE categories
        SET
        parent_id=?,
        category_name=?,
        slug=?,
        status=?,
        updated_at=NOW()
        WHERE id=?
        ");

        $update->execute([
            $parent_id,
            $name,
            $slug,
            $status,
            $id
        ]);

        $_SESSION['success'] = "Category Updated Successfully.";

        header("Location: index.php");
        exit;

    }

}

require_once '../../includes/header.php';

?><div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">
<i class="fa fa-edit"></i> Edit Category
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

Parent Category

</label>

<select
name="parent_id"
class="form-select">

<option value="">Main Category</option>

<?php foreach($parents as $parent){ ?>

<option
value="<?= $parent['id']; ?>"

<?= ($category['parent_id']==$parent['id']) ? 'selected' : ''; ?>>

<?= htmlspecialchars($parent['category_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Category Name

</label>

<input
type="text"
name="category_name"
class="form-control"
required
value="<?= htmlspecialchars($category['category_name']); ?>">

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
value="<?= htmlspecialchars($category['slug']); ?>">

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

<?= ($category['status']=="Active") ? "selected" : ""; ?>>

Active

</option>

<option
value="Inactive"

<?= ($category['status']=="Inactive") ? "selected" : ""; ?>>

Inactive

</option>

</select>

</div>

<div class="mt-4">

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Update Category

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

<?php require_once '../../includes/footer.php';