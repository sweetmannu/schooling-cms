<?php

http_response_code(404);

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| 404 Page SEO
|--------------------------------------------------------------------------
*/

$pageTitle = '404 - Page Not Found | ' . APP_NAME;

$pageDescription = 'The requested page could not be found.';

$robotsMeta = 'noindex,follow';

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';

?>

<main class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-body text-center p-4 p-md-5">

                    <div
                        class="display-1 text-danger mb-3"
                        aria-hidden="true">

                        <i class="fa fa-triangle-exclamation"></i>

                    </div>

                    <h1 class="fw-bold display-4">

                        404

                    </h1>

                    <h2 class="h3 mb-3">

                        Page Not Found

                    </h2>

                    <p class="text-muted mb-4">

                        The page you are looking for does not exist,
                        may have been moved, or is no longer available.

                    </p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">

                        <a
                            href="<?= htmlspecialchars(
                                rtrim(APP_URL, '/') . '/',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="btn btn-primary">

                            <i class="fa fa-house me-1"></i>

                            Home

                        </a>

                        <a
                            href="<?= htmlspecialchars(
                                rtrim(APP_URL, '/') . '/search.php',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="btn btn-outline-primary">

                            <i class="fa fa-search me-1"></i>

                            Search Notes

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>