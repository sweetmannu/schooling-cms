<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$stmt = $pdo->query("
SELECT
    chapters.*,
    subjects.subject_name
FROM chapters
INNER JOIN subjects
ON chapters.subject_id = subjects.id
ORDER BY chapters.chapter_order ASC, chapters.id DESC
");

$chapters = $stmt->fetchAll();

require_once '../../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">
<i class="fa fa-list"></i> Chapters
</h4>

<a href="create.php" class="btn btn-success">
<i class="fa fa-plus"></i> Add Chapter
</a>

</div>

<div class="card-body">

<?php require_once '../../includes/flash.php'; ?>

<table id="dataTable" class="table table-bordered table-hover mb-0">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Subject</th>

<th>Chapter</th>

<th width="90">Order</th>

<th width="100">Status</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($chapters) > 0){ ?>

<?php foreach($chapters as $row){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['subject_name']); ?></td>

<td><?= htmlspecialchars($row['chapter_name']); ?></td>

<td><?= $row['chapter_order']; ?></td>

<td>

<?php if($row['status']=="Active"){ ?>

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
onclick="return confirm('Delete this chapter?')">

<i class="fa fa-trash"></i> Delete

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="6" class="text-center">

No Chapters Found.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

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