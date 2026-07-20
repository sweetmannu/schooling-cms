<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/header.php';

$stmt = $pdo->query("
SELECT
    c1.id,
    c1.category_name,
    c1.slug,
    c1.status,
    c2.category_name AS parent_category
FROM categories c1
LEFT JOIN categories c2
ON c1.parent_id = c2.id
ORDER BY c1.id DESC
");

$categories = $stmt->fetchAll();

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="fa fa-folder"></i> Categories
</h2>

<a href="create.php" class="btn btn-success">
    <i class="fa fa-plus"></i> Add Category
</a>

</div>

<?php require_once '../../includes/flash.php'; ?>

<table id="dataTable" class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Category</th>

<th>Parent</th>

<th>Slug</th>

<th>Status</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($categories) > 0){ ?>

<?php foreach($categories as $row){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['category_name']); ?></td>

<td>

<?= $row['parent_category']
    ? htmlspecialchars($row['parent_category'])
    : '<span class="badge bg-primary">Main Category</span>'; ?>

</td>

<td><?= htmlspecialchars($row['slug']); ?></td>

<td>

<?php if($row['status'] == "Active"){ ?>

<span class="badge bg-success">Active</span>

<?php } else { ?>

<span class="badge bg-danger">Inactive</span>

<?php } ?>

</td>

<td>

<a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
    <i class="fa fa-edit"></i> Edit
</a>

<a
href="delete.php?id=<?= $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this category?')">

<i class="fa fa-trash"></i> Delete

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="6" class="text-center">

No Categories Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>

<script>
$(document).ready(function () {

    $('#dataTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true
    });

});
</script>