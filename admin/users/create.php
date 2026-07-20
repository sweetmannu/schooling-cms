<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/header.php';

$errors = [];

$name = '';
$email = '';
$role = 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = trim($_POST['role'] ?? 'admin');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password and confirm password do not match.';
    }

    $allowedRoles = ['admin', 'editor'];

    if (!in_array($role, $allowedRoles, true)) {
        $errors[] = 'Invalid user role selected.';
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $errors[] = 'A user with this email already exists.';

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    role
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $role
            ]);

            $_SESSION['success'] = 'User created successfully.';

            header('Location: index.php');
            exit;
        }
    }
}

?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
    <i class="fa fa-user-plus"></i> Add User
</h2>

<a href="index.php" class="btn btn-secondary">
    <i class="fa fa-arrow-left"></i> Back
</a>

</div>

<?php if (!empty($errors)) { ?>

<div class="alert alert-danger">

<ul class="mb-0">

<?php foreach ($errors as $error) { ?>

<li>
    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
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
value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
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
value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
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

<div class="mb-3">

<label class="form-label">
    Password
</label>

<input
type="password"
name="password"
class="form-control"
required
autocomplete="new-password">

<small class="text-muted">
    Minimum 8 characters
</small>

</div>

<div class="mb-3">

<label class="form-label">
    Confirm Password
</label>

<input
type="password"
name="confirm_password"
class="form-control"
required
autocomplete="new-password">

</div>

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>
Create User

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