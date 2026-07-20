<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/header.php';

/*
|--------------------------------------------------------------------------
| Validate User ID
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch Existing User
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        name,
        email,
        role
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: index.php');
    exit;
}

$errors = [];

$name  = $user['name'];
$email = $user['email'];
$role  = $user['role'];

/*
|--------------------------------------------------------------------------
| Handle Update
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '') {

        $errors[] = 'Email is required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors[] = 'Please enter a valid email address.';
    }

    $allowedRoles = [
        'admin',
        'editor'
    ];

    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = 'Invalid user role selected.';
    }

    /*
    |--------------------------------------------------------------------------
    | Password is Optional During Edit
    |--------------------------------------------------------------------------
    */

    if ($password !== '') {

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Password and confirm password do not match.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check Duplicate Email
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            AND id != ?
            LIMIT 1
        ");

        $stmt->execute([
            $email,
            $id
        ]);

        if ($stmt->fetch()) {
            $errors[] = 'Another user already uses this email address.';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        if ($password !== '') {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    role = ?,
                    password = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $email,
                $role,
                $hashedPassword,
                $id
            ]);

        } else {

            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    role = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $email,
                $role,
                $id
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Refresh Current Session if Editing Own Account
        |--------------------------------------------------------------------------
        */

        if (
            isset($_SESSION['admin']['id']) &&
            (int) $_SESSION['admin']['id'] === $id
        ) {

            $_SESSION['admin']['name'] = $name;
            $_SESSION['admin']['email'] = $email;
            $_SESSION['admin']['role'] = $role;
        }

        $_SESSION['success'] = 'User updated successfully.';

        header('Location: index.php');
        exit;
    }
}

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="fa fa-user-edit"></i>
    Edit User
</h2>

<a href="index.php" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i>
    Back
</a>

</div>

<?php if (!empty($errors)) { ?>

<div class="alert alert-danger">

<ul class="mb-0">

<?php foreach ($errors as $error) { ?>

<li>
    <?= htmlspecialchars(
        $error,
        ENT_QUOTES,
        'UTF-8'
    ); ?>
</li>

<?php } ?>

</ul>

</div>

<?php } ?>

<div class="card shadow-sm">

<div class="card-body">

<form method="POST" autocomplete="off">

<div class="mb-3">

<label class="form-label">
    Name
</label>

<input
type="text"
name="name"
class="form-control"
value="<?= htmlspecialchars(
    $name,
    ENT_QUOTES,
    'UTF-8'
); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">
    Email
</label>

<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars(
    $email,
    ENT_QUOTES,
    'UTF-8'
); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">
    Role
</label>

<select
name="role"
class="form-select"
required>

<option
value="admin"
<?= $role === 'admin' ? 'selected' : ''; ?>
>
Admin
</option>

<option
value="editor"
<?= $role === 'editor' ? 'selected' : ''; ?>
>
Editor
</option>

</select>

</div>

<hr>

<h5 class="mb-3">
    Change Password
</h5>

<p class="text-muted">
    Leave password fields blank if you do not want to change the password.
</p>

<div class="mb-3">

<label class="form-label">
    New Password
</label>

<input
type="password"
name="password"
class="form-control"
autocomplete="new-password">

<small class="text-muted">
    Minimum 8 characters
</small>

</div>

<div class="mb-3">

<label class="form-label">
    Confirm New Password
</label>

<input
type="password"
name="confirm_password"
class="form-control"
autocomplete="new-password">

</div>

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>
Update User

</button>

<a
href="index.php"
class="btn btn-light border">

Cancel

</a>

</form>

</div>

</div>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>