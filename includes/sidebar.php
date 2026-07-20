<?php

$currentPage = $_SERVER['REQUEST_URI'];

function isActive($keyword)
{
    global $currentPage;

    return (strpos($currentPage, $keyword) !== false)
        ? 'bg-primary rounded'
        : '';
}

?>

<div class="bg-dark text-white p-3 shadow" style="width:260px; min-height:100vh;">

    <h4 class="text-center mb-4">
        <i class="fa fa-graduation-cap"></i>
        Schooling CMS
    </h4>

    <hr>

    <ul class="nav flex-column">

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/dashboard.php"
               class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary rounded' : ''; ?>">

                <i class="fa fa-home me-2"></i>

                Dashboard

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/categories/index.php"
               class="nav-link text-white <?= isActive('categories'); ?>">

                <i class="fa fa-folder me-2"></i>

                Categories

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/subjects/index.php"
               class="nav-link text-white <?= isActive('subjects'); ?>">

                <i class="fa fa-book me-2"></i>

                Subjects

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/chapters/index.php"
               class="nav-link text-white <?= isActive('chapters'); ?>">

                <i class="fa fa-list me-2"></i>

                Chapters

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/notes/index.php"
               class="nav-link text-white <?= isActive('notes'); ?>">

                <i class="fa fa-file-alt me-2"></i>

                Notes

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/users/index.php"
               class="nav-link text-white <?= isActive('users'); ?>">

                <i class="fa fa-users me-2"></i>

                Users

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/settings/index.php"
               class="nav-link text-white <?= isActive('settings'); ?>">

                <i class="fa fa-cog me-2"></i>

                Settings

            </a>

        </li>

        <hr>

        <li class="nav-item">

            <a href="<?= APP_URL; ?>/admin/logout.php"
               class="nav-link text-danger">

                <i class="fa fa-sign-out-alt me-2"></i>

                Logout

            </a>

        </li>

    </ul>

</div>