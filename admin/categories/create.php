<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';

$parentCategories = $pdo->query("
SELECT id, category_name
FROM categories
WHERE parent_id IS NULL
ORDER BY category_name ASC
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    verify_csrf_token($_POST['csrf_token'] ?? '');

    $parent_id = !empty($_POST['parent_id'])
        ? (int)$_POST['parent_id']
        : NULL;

    $name = trim(strip_tags($_POST['category_name']));
    $slug = strtolower(trim($_POST['slug']));
$slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
$slug = trim($slug, '-');
    $status = (($_POST['status'] ?? 'Active') === 'Inactive')
    ? 'Inactive'
    : 'Active';


    // Validation
    if (empty($name) || empty($slug)) {
        $error = "Category Name and Slug are required.";
    }

     if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM categories
    WHERE category_name=?
    ");

    $check->execute([$name]);

    if ($check->fetch()) {

        $error = "Category already exists.";

    }

}

    // Duplicate Slug Check
    if (empty($error)) {

        $check = $pdo->prepare("
        SELECT id
        FROM categories
        WHERE slug=?
        ");

        $check->execute([$slug]);

        if ($check->fetch()) {
            $error = "Slug already exists.";
        }

    }

    // Save Category
    if (empty($error)) {

        $stmt = $pdo->prepare("
        INSERT INTO categories
        (
            parent_id,
            category_name,
            slug,
            status
        )
        VALUES
        (?,?,?,?)
        ");

        $stmt->execute([
            $parent_id,
            $name,
            $slug,
            $status
        ]);

        $_SESSION['success'] = "Category Added Successfully.";

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
<i class="fa fa-folder-plus"></i> Add Category
</h4>

</div>

<div class="card-body">

<?php if (!empty($error)) { ?>

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

Parent Category

</label>

<select
name="parent_id"
class="form-select">

<option value="">Main Category</option>

<?php foreach ($parentCategories as $parent) { ?>

<option
value="<?= $parent['id']; ?>"
<?= (($_POST['parent_id'] ?? '') == $parent['id']) ? 'selected' : ''; ?>>

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
value="<?= htmlspecialchars($_POST['category_name'] ?? ''); ?>"
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
value="<?= htmlspecialchars($_POST['slug'] ?? ''); ?>"
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

<div class="mt-4">

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Save Category

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