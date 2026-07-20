<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

/*
|--------------------------------------------------------------------------
| Get All Subjects with Category Name
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
SELECT
    subjects.*,
    categories.category_name
FROM subjects
LEFT JOIN categories
ON subjects.category_id = categories.id
ORDER BY subjects.id DESC
");

$subjects = $stmt->fetchAll();

require_once '../../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="fa fa-book"></i> Subjects
</h2>

<a href="create.php" class="btn btn-success">
    <i class="fa fa-plus"></i> Add Subject
</a>

</div>

<?php require_once '../../includes/flash.php'; ?>

<table id="dataTable" class="table table-bordered table-hover shadow">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Category</th>

<th>Subject Name</th>

<th>Slug</th>

<th>Status</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($subjects) > 0){ ?>

<?php foreach($subjects as $row){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['category_name']); ?></td>

<td><?= htmlspecialchars($row['subject_name']); ?></td>

<td><?= htmlspecialchars($row['slug']); ?></td>

<td>

<?php if($row['status'] == "Active"){ ?>

<span class="badge bg-success">Active</span>

<?php } else { ?>

<span class="badge bg-danger">Inactive</span>

<?php } ?>

</td>

<td>

<a
href="edit.php?id=<?= $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fa fa-edit"></i> Edit

</a>

<a
href="delete.php?id=<?= $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this subject?')">

<i class="fa fa-trash"></i> Delete

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="6" class="text-center">

No Subjects Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>

<script>
$(function () {

    $('#dataTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        responsive: true,
        autoWidth: false
    });

});
</script>