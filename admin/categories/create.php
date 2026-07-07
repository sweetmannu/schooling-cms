<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$parentCategories = $pdo->query("
SELECT id, category_name
FROM categories
WHERE parent_id IS NULL
ORDER BY category_name
")->fetchAll();

if($_SERVER['REQUEST_METHOD']=="POST"){

$parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL;

$name = trim($_POST['category_name']);

$slug = strtolower(trim($_POST['slug']));

$status = $_POST['status'];

$check = $pdo->prepare("SELECT id FROM categories WHERE slug=?");

$check->execute([$slug]);

if($check->fetch()){

$error="Slug already exists.";

}else{

$stmt=$pdo->prepare("
INSERT INTO categories
(parent_id,category_name,slug,status)
VALUES(?,?,?,?)
");

$stmt->execute([
$parent_id,
$name,
$slug,
$status
]);

header("Location:index.php");

exit;

}

}

require_once '../../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<h2>Add Category</h2>

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label>Parent Category</label>

<select
name="parent_id"
class="form-select">

<option value="">Main Category</option>

<?php foreach($parentCategories as $parent){ ?>

<option value="<?= $parent['id']; ?>">

<?= htmlspecialchars($parent['category_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>Category Name</label>

<input
type="text"
name="category_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Slug</label>

<input
type="text"
name="slug"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

<button
type="submit"
class="btn btn-primary">

Save Category

</button>

<a
href="index.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>