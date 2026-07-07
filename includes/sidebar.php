<?php

$currentPage = basename($_SERVER['PHP_SELF']);

?>

<div class="bg-dark text-white p-3" style="width:260px; min-height:100vh;">

    <h4 class="text-center mb-4">
        Schooling CMS
    </h4>

    <hr>

    <ul class="nav flex-column">

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/dashboard.php"
               class="nav-link text-white <?= ($currentPage=='dashboard.php') ? 'bg-primary rounded' : ''; ?>">

                <i class="fa fa-home me-2"></i>

                Dashboard

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/categories/index.php"
               class="nav-link text-white <?= (strpos($_SERVER['REQUEST_URI'],'categories')!==false) ? 'bg-primary rounded' : ''; ?>">

                <i class="fa fa-folder me-2"></i>

                Categories

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/subjects/index.php"
               class="nav-link text-white">

                <i class="fa fa-book me-2"></i>

                Subjects

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/chapters/index.php"
               class="nav-link text-white">

                <i class="fa fa-list me-2"></i>

                Chapters

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/notes/index.php"
               class="nav-link text-white">

                <i class="fa fa-file-alt me-2"></i>

                Notes

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/users/index.php"
               class="nav-link text-white">

                <i class="fa fa-users me-2"></i>

                Users

            </a>

        </li>

        <li class="nav-item mb-2">

            <a href="<?= APP_URL; ?>/admin/settings/index.php"
               class="nav-link text-white">

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