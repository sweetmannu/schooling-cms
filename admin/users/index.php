<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/header.php';

$stmt = $pdo->query("
SELECT
    id,
    name,
    email,
    role,
    created_at
FROM users
ORDER BY id DESC
");

$users = $stmt->fetchAll();

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="fa fa-users"></i> Users
</h2>

<a href="create.php" class="btn btn-success">
    <i class="fa fa-plus"></i> Add User
</a>

</div>

<?php require_once '../../includes/flash.php'; ?>

<table id="dataTable" class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Name</th>

<th>Email</th>

<th width="120">Role</th>

<th width="180">Created At</th>

<th width="200">Action</th>

</tr>

</thead>

<tbody>

<?php if (count($users) > 0) { ?>

<?php foreach ($users as $row) { ?>

<tr>

<td><?= (int) $row['id']; ?></td>

<td>
    <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
</td>

<td>
    <?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>
</td>

<td>

<?php

$role = strtolower(trim((string) $row['role']));

if ($role === 'admin') {

    $badgeClass = 'bg-danger';

} elseif ($role === 'editor') {

    $badgeClass = 'bg-warning text-dark';

} else {

    $badgeClass = 'bg-secondary';

}

?>

<span class="badge <?= $badgeClass; ?>">
    <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8'); ?>
</span>

</td>

<td>

<?php

if (!empty($row['created_at'])) {

    echo htmlspecialchars(
        date(
            'd M Y, h:i A',
            strtotime($row['created_at'])
        ),
        ENT_QUOTES,
        'UTF-8'
    );

} else {

    echo '-';

}

?>

</td>

<td>

<a
href="edit.php?id=<?= (int) $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fa fa-edit"></i> Edit

</a>

<?php

$currentAdminId =
    isset($_SESSION['admin']['id'])
    ? (int) $_SESSION['admin']['id']
    : 0;

?>

<?php if ((int) $row['id'] !== $currentAdminId) { ?>

<a
href="delete.php?id=<?= (int) $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')">

<i class="fa fa-trash"></i> Delete

</a>

<?php } else { ?>

<button
type="button"
class="btn btn-secondary btn-sm"
disabled>

<i class="fa fa-lock"></i> Current

</button>

<?php } ?>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>

<td colspan="6" class="text-center">

No Users Found

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