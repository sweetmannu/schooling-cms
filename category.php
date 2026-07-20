<?php

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| Validate Slug
|--------------------------------------------------------------------------
*/

$rawSlug = $_GET['slug'] ?? '';

if (
    !is_string($rawSlug) ||
    trim($rawSlug) === ''
) {
    http_response_code(404);

    $pageTitle = 'Category Not Found | ' . APP_NAME;
    $pageDescription = 'The requested category could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center">

            <h1 class="h4 mb-2">
                Category Not Found
            </h1>

            <p class="mb-0">
                The requested category is invalid or unavailable.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Sanitize Slug
|--------------------------------------------------------------------------
*/

$slug = strtolower(trim($rawSlug));
$slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
$slug = preg_replace('/-+/', '-', $slug);
$slug = trim($slug, '-');

if ($slug === '') {
    http_response_code(404);

    $pageTitle = 'Category Not Found | ' . APP_NAME;
    $pageDescription = 'The requested category could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center">

            Category Not Found.

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Main Category
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        category_name,
        slug
    FROM categories
    WHERE slug = ?
      AND status = 'Active'
      AND parent_id IS NULL
    LIMIT 1
");

$stmt->execute([$slug]);

$category = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Category Not Found
|--------------------------------------------------------------------------
*/

if (!$category) {
    http_response_code(404);

    $pageTitle = 'Category Not Found | ' . APP_NAME;
    $pageDescription = 'The requested category could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center">

            <h1 class="h4 mb-2">
                Category Not Found
            </h1>

            <p class="mb-0">
                This category does not exist or is currently unavailable.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Subjects
|--------------------------------------------------------------------------
*/

$subjectStmt = $pdo->prepare("
    SELECT
        id,
        subject_name,
        slug,
        icon,
        description
    FROM subjects
    WHERE category_id = ?
      AND status = 'Active'
    ORDER BY subject_name ASC
");

$subjectStmt->execute([$category['id']]);

$subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Page SEO
|--------------------------------------------------------------------------
*/

$pageTitle = $category['category_name'] . ' | ' . APP_NAME;

$pageDescription =
    'Browse available subjects, chapters and study notes under '
    . $category['category_name']
    . '.';

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';
require_once __DIR__ . '/includes/frontend/breadcrumb.php';

?>

<main class="container py-5">

    <!-- Page Header -->

    <section class="text-center mb-5">

        <h1 class="display-5 fw-bold mb-3">

            <?= htmlspecialchars(
                $category['category_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </h1>

        <p class="lead text-muted mb-0">

            Browse all available subjects under this category.

        </p>

    </section>

    <!-- Subjects -->

    <section aria-labelledby="subjects-heading">

        <h2
            id="subjects-heading"
            class="h3 fw-bold mb-4">

            <i
                class="fa fa-book-open text-primary me-2"
                aria-hidden="true"></i>

            Available Subjects

        </h2>

        <div class="row">

            <?php if (count($subjects) > 0) { ?>

                <?php foreach ($subjects as $subject) { ?>

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                        <article
                            class="card shadow-sm border-0 rounded-4 h-100 text-center">

                            <div class="card-body d-flex flex-column p-4">

                                <div
                                    class="display-4 text-primary mb-3"
                                    aria-hidden="true">

                                    <?php if (!empty($subject['icon'])) { ?>

                                        <i class="<?= htmlspecialchars(
                                            $subject['icon'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"></i>

                                    <?php } else { ?>

                                        <i class="fa fa-book"></i>

                                    <?php } ?>

                                </div>

                                <h3 class="h5 fw-bold">

                                    <?= htmlspecialchars(
                                        $subject['subject_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </h3>

                                <?php if (!empty($subject['description'])) { ?>

                                    <p class="text-muted small">

                                        <?= htmlspecialchars(
                                            mb_strimwidth(
                                                strip_tags(
                                                    $subject['description']
                                                ),
                                                0,
                                                100,
                                                '...',
                                                'UTF-8'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </p>

                                <?php } ?>

                                <div class="mt-auto pt-3">

                                    <a
                                        href="<?= htmlspecialchars(
                                            rtrim(APP_URL, '/')
                                            . '/subject/'
                                            . rawurlencode(
                                                $subject['slug']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        class="btn btn-primary btn-sm">

                                        View Chapters

                                        <i
                                            class="fa fa-arrow-right ms-1"
                                            aria-hidden="true"></i>

                                    </a>

                                </div>

                            </div>

                        </article>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="col-12">

                    <div class="alert alert-warning text-center py-4">

                        <h3 class="h5 mb-2">

                            No Subjects Available

                        </h3>

                        <p class="mb-0">

                            No active subjects are currently available under
                            this category.

                        </p>

                    </div>

                </div>

            <?php } ?>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>