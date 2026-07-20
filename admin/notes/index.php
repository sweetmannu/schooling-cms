<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$stmt = $pdo->query("
SELECT
    notes.*,
    chapters.chapter_name,
    subjects.subject_name,
    categories.category_name
FROM notes
INNER JOIN chapters
ON notes.chapter_id = chapters.id
INNER JOIN subjects
ON chapters.subject_id = subjects.id
INNER JOIN categories
ON subjects.category_id = categories.id
ORDER BY notes.id DESC
");

$notes = $stmt->fetchAll();

require_once '../../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">
<i class="fa fa-file-alt"></i> Notes
</h4>

<a href="create.php" class="btn btn-success">
<i class="fa fa-plus"></i> Add Notes
</a>

</div>

<div class="card-body">

<?php require_once '../../includes/flash.php'; ?>

<table id="dataTable" class="table table-bordered table-hover mb-0">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Category</th>

<th>Subject</th>

<th>Chapter</th>

<th>Title</th>

<th width="100">Status</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($notes) > 0){ ?>

<?php foreach($notes as $row){ ?>

<tr>

<td><?= $row['id']; ?></td>

<td><?= htmlspecialchars($row['category_name']); ?></td>

<td><?= htmlspecialchars($row['subject_name']); ?></td>

<td><?= htmlspecialchars($row['chapter_name']); ?></td>

<td><?= htmlspecialchars($row['title']); ?></td>

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

<form method="POST"
      action="delete.php"
      style="display:inline;">

<input
type="hidden"
name="csrf_token"
value="<?= csrf_token(); ?>">

<input
type="hidden"
name="id"
value="<?= $row['id']; ?>">

<button
type="submit"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this note?');">

<i class="fa fa-trash"></i>
Delete

</button>

</form>
</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="7" class="text-center">

No Notes Found

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