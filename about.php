<?php

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

$pageTitle = 'About Us - ' . APP_NAME;

$pageDescription =
    'Learn more about ' . APP_NAME .
    ', our educational mission, learning resources, and commitment to helping students study better.';

$robots = 'index,follow';

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';

?>

<main>

    <!-- ======================================================
         Page Header
    ======================================================= -->

    <section class="py-5 bg-light border-bottom">

        <div class="container">

            <div class="text-center">

                <h1 class="fw-bold mb-3">
                    About Us
                </h1>

                <p class="lead text-muted mb-0">

                    Helping students learn better with organized,
                    accessible and useful educational resources.

                </p>

            </div>

        </div>

    </section>


    <!-- ======================================================
         Breadcrumb
    ======================================================= -->

    <section class="py-3">

        <div class="container">

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb mb-0">

                    <li class="breadcrumb-item">

                        <a href="<?= htmlspecialchars(
                            rtrim(APP_URL, '/') . '/',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>">

                            Home

                        </a>

                    </li>

                    <li
                        class="breadcrumb-item active"
                        aria-current="page"
                    >
                        About
                    </li>

                </ol>

            </nav>

        </div>

    </section>


    <!-- ======================================================
         About Content
    ======================================================= -->

    <section class="py-5">

        <div class="container">

            <div class="row justify-content-center">

                <div class="col-lg-10">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-4 p-md-5">

                            <h2 class="fw-bold mb-3">
                                Welcome to
                                <?= htmlspecialchars(
                                    APP_NAME,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </h2>

                            <p class="text-muted">

                                <?= htmlspecialchars(
                                    APP_NAME,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                                is an educational platform designed to make
                                learning content easier to discover and use.

                            </p>

                            <p class="text-muted">

                                Our goal is to organize study material in a
                                simple structure of categories, subjects,
                                chapters and notes so students can quickly
                                reach the information they need.

                            </p>

                            <hr class="my-4">

                            <h3 class="h4 fw-bold mb-3">
                                Our Mission
                            </h3>

                            <p class="text-muted">

                                Our mission is to provide students with
                                clear, structured and accessible educational
                                resources that support independent learning
                                and academic growth.

                            </p>

                            <div class="row g-4 mt-2">

                                <div class="col-md-4">

                                    <div class="h-100 p-4 border rounded">

                                        <div class="fs-2 mb-3">

                                            <i class="fa fa-book"></i>

                                        </div>

                                        <h4 class="h5 fw-bold">
                                            Organized Learning
                                        </h4>

                                        <p class="text-muted mb-0">

                                            Content arranged by category,
                                            subject and chapter for easier
                                            navigation.

                                        </p>

                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="h-100 p-4 border rounded">

                                        <div class="fs-2 mb-3">

                                            <i class="fa fa-graduation-cap"></i>

                                        </div>

                                        <h4 class="h5 fw-bold">
                                            Student Focused
                                        </h4>

                                        <p class="text-muted mb-0">

                                            Resources designed to make
                                            studying simpler and more useful.

                                        </p>

                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="h-100 p-4 border rounded">

                                        <div class="fs-2 mb-3">

                                            <i class="fa fa-mobile-alt"></i>

                                        </div>

                                        <h4 class="h5 fw-bold">
                                            Easy Access
                                        </h4>

                                        <p class="text-muted mb-0">

                                            A responsive platform that can be
                                            accessed across phones, tablets
                                            and computers.

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>