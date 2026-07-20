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

    $pageTitle = 'Subject Not Found | ' . APP_NAME;
    $pageDescription = 'The requested subject could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Subject Not Found
            </h1>

            <p class="mb-0">
                The requested subject is invalid or unavailable.
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

$slug = preg_replace(
    '/[^a-z0-9-]+/',
    '-',
    $slug
);

$slug = preg_replace(
    '/-+/',
    '-',
    $slug
);

$slug = trim($slug, '-');

if ($slug === '') {
    http_response_code(404);

    $pageTitle = 'Subject Not Found | ' . APP_NAME;
    $pageDescription = 'The requested subject could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Subject Not Found
            </h1>

            <p class="mb-0">
                The requested subject is invalid.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Subject and Category
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        subjects.id,
        subjects.category_id,
        subjects.subject_name,
        subjects.slug,
        subjects.description,

        categories.category_name,
        categories.slug AS category_slug

    FROM subjects

    INNER JOIN categories
        ON categories.id = subjects.category_id

    WHERE subjects.slug = ?
      AND subjects.status = 'Active'
      AND categories.status = 'Active'

    LIMIT 1
");

$stmt->execute([$slug]);

$subject = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Subject Not Found
|--------------------------------------------------------------------------
*/

if (!$subject) {
    http_response_code(404);

    $pageTitle = 'Subject Not Found | ' . APP_NAME;
    $pageDescription = 'The requested subject could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Subject Not Found
            </h1>

            <p class="mb-0">
                This subject does not exist or is currently unavailable.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Chapters
|--------------------------------------------------------------------------
*/

$chapterStmt = $pdo->prepare("
    SELECT
        id,
        chapter_name,
        slug,
        description,
        chapter_order
    FROM chapters
    WHERE subject_id = ?
      AND status = 'Active'
    ORDER BY
        chapter_order ASC,
        chapter_name ASC
");

$chapterStmt->execute([$subject['id']]);

$chapters = $chapterStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Page SEO
|--------------------------------------------------------------------------
*/

$pageTitle =
    $subject['subject_name']
    . ' | '
    . APP_NAME;

$pageDescription = !empty($subject['description'])
    ? mb_strimwidth(
        strip_tags($subject['description']),
        0,
        160,
        '...',
        'UTF-8'
    )
    : (
        'Browse chapters and study notes for '
        . $subject['subject_name']
        . '.'
    );

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';
require_once __DIR__ . '/includes/frontend/breadcrumb.php';

?>

<main class="container py-5">

    <!-- Page Header -->

    <section class="text-center mb-5">

        <p class="text-primary fw-semibold mb-2">

            <?= htmlspecialchars(
                $subject['category_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </p>

        <h1 class="display-5 fw-bold mb-3">

            <?= htmlspecialchars(
                $subject['subject_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </h1>

        <?php if (!empty($subject['description'])) { ?>

            <p class="lead text-muted mx-auto mb-0"
               style="max-width:850px;">

                <?= nl2br(
                    htmlspecialchars(
                        $subject['description'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ); ?>

            </p>

        <?php } else { ?>

            <p class="lead text-muted mb-0">

                Browse all available chapters under this subject.

            </p>

        <?php } ?>

    </section>

    <!-- Chapters -->

    <section aria-labelledby="chapters-heading">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2
                id="chapters-heading"
                class="h3 fw-bold mb-0">

                <i
                    class="fa fa-book-open text-success me-2"
                    aria-hidden="true"></i>

                Available Chapters

            </h2>

            <?php if (count($chapters) > 0) { ?>

                <span class="badge bg-success">

                    <?= count($chapters); ?>

                    <?= count($chapters) === 1
                        ? 'Chapter'
                        : 'Chapters'; ?>

                </span>

            <?php } ?>

        </div>

        <div class="row">

            <?php if (count($chapters) > 0) { ?>

                <?php foreach ($chapters as $chapter) { ?>

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                        <article
                            class="card shadow-sm border-0 rounded-4 h-100 text-center">

                            <div class="card-body d-flex flex-column p-4">

                                <div
                                    class="display-4 text-success mb-3"
                                    aria-hidden="true">

                                    <i class="fa fa-book-open"></i>

                                </div>

                                <h3 class="h5 fw-bold">

                                    <?= htmlspecialchars(
                                        $chapter['chapter_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </h3>

                                <?php if (!empty($chapter['description'])) { ?>

                                    <p class="small text-muted">

                                        <?= htmlspecialchars(
                                            mb_strimwidth(
                                                strip_tags(
                                                    $chapter['description']
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
                                            . '/chapter/'
                                            . rawurlencode(
                                                $chapter['slug']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        class="btn btn-success btn-sm">

                                        View Notes

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

                            No Chapters Available

                        </h3>

                        <p class="mb-0">

                            No active chapters are currently available under
                            this subject.

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