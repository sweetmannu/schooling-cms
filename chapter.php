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

    $pageTitle = 'Chapter Not Found | ' . APP_NAME;
    $pageDescription = 'The requested chapter could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Chapter Not Found
            </h1>

            <p class="mb-0">
                The requested chapter is invalid or unavailable.
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

    $pageTitle = 'Chapter Not Found | ' . APP_NAME;
    $pageDescription = 'The requested chapter could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Chapter Not Found
            </h1>

            <p class="mb-0">
                The requested chapter is invalid.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Chapter, Subject and Category
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        chapters.id,
        chapters.subject_id,
        chapters.chapter_name,
        chapters.slug,
        chapters.description,
        chapters.chapter_order,

        subjects.subject_name,
        subjects.slug AS subject_slug,

        categories.category_name,
        categories.slug AS category_slug

    FROM chapters

    INNER JOIN subjects
        ON subjects.id = chapters.subject_id

    INNER JOIN categories
        ON categories.id = subjects.category_id

    WHERE chapters.slug = ?
      AND chapters.status = 'Active'
      AND subjects.status = 'Active'
      AND categories.status = 'Active'

    LIMIT 1
");

$stmt->execute([$slug]);

$chapter = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Chapter Not Found
|--------------------------------------------------------------------------
*/

if (!$chapter) {
    http_response_code(404);

    $pageTitle = 'Chapter Not Found | ' . APP_NAME;
    $pageDescription = 'The requested chapter could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Chapter Not Found
            </h1>

            <p class="mb-0">
                This chapter does not exist or is currently unavailable.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Notes
|--------------------------------------------------------------------------
*/

$noteStmt = $pdo->prepare("
    SELECT
        id,
        title,
        slug,
        short_description,
        thumbnail,
        created_at
    FROM notes
    WHERE chapter_id = ?
      AND status = 'Active'
    ORDER BY created_at DESC, id DESC
");

$noteStmt->execute([$chapter['id']]);

$notes = $noteStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Page SEO
|--------------------------------------------------------------------------
*/

$pageTitle =
    $chapter['chapter_name']
    . ' | '
    . APP_NAME;

$pageDescription = !empty($chapter['description'])
    ? mb_strimwidth(
        strip_tags($chapter['description']),
        0,
        160,
        '...',
        'UTF-8'
    )
    : (
        'Browse study notes for '
        . $chapter['chapter_name']
        . ' under '
        . $chapter['subject_name']
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
                $chapter['category_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

            <span class="mx-1" aria-hidden="true">/</span>

            <?= htmlspecialchars(
                $chapter['subject_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </p>

        <h1 class="display-5 fw-bold mb-3">

            <?= htmlspecialchars(
                $chapter['chapter_name'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </h1>

        <?php if (!empty($chapter['description'])) { ?>

            <p
                class="lead text-muted mx-auto mb-0"
                style="max-width:850px;">

                <?= nl2br(
                    htmlspecialchars(
                        $chapter['description'],
                        ENT_QUOTES,
                        'UTF-8'
                    )
                ); ?>

            </p>

        <?php } else { ?>

            <p class="lead text-muted mb-0">

                Browse all available study notes under this chapter.

            </p>

        <?php } ?>

    </section>

    <!-- Notes -->

    <section aria-labelledby="notes-heading">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h2
                id="notes-heading"
                class="h3 fw-bold mb-0">

                <i
                    class="fa fa-file-lines text-primary me-2"
                    aria-hidden="true"></i>

                Available Notes

            </h2>

            <?php if (count($notes) > 0) { ?>

                <span class="badge bg-primary">

                    <?= count($notes); ?>

                    <?= count($notes) === 1
                        ? 'Note'
                        : 'Notes'; ?>

                </span>

            <?php } ?>

        </div>

        <div class="row">

            <?php if (count($notes) > 0) { ?>

                <?php foreach ($notes as $note) { ?>

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                        <article
                            class="card shadow-sm border-0 rounded-4 h-100">

                            <?php if (!empty($note['thumbnail'])) { ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        rtrim(APP_URL, '/')
                                        . '/uploads/thumbnails/'
                                        . rawurlencode(
                                            $note['thumbnail']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    alt="<?= htmlspecialchars(
                                        $note['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="card-img-top"
                                    style="height:180px;object-fit:cover;"
                                    loading="lazy">

                            <?php } ?>

                            <div class="card-body d-flex flex-column text-center p-4">

                                <?php if (empty($note['thumbnail'])) { ?>

                                    <div
                                        class="display-4 text-primary mb-3"
                                        aria-hidden="true">

                                        <i class="fa fa-file-lines"></i>

                                    </div>

                                <?php } ?>

                                <h3 class="h5 fw-bold">

                                    <?= htmlspecialchars(
                                        $note['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </h3>

                                <?php if (!empty($note['created_at'])) { ?>

                                    <p class="small text-muted mb-2">

                                        <i
                                            class="fa fa-calendar me-1"
                                            aria-hidden="true"></i>

                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y',
                                                strtotime(
                                                    $note['created_at']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </p>

                                <?php } ?>

                                <?php if (!empty($note['short_description'])) { ?>

                                    <p class="text-muted small">

                                        <?= htmlspecialchars(
                                            mb_strimwidth(
                                                strip_tags(
                                                    $note['short_description']
                                                ),
                                                0,
                                                110,
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
                                            . '/note/'
                                            . rawurlencode(
                                                $note['slug']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        class="btn btn-primary btn-sm">

                                        Read Note

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

                            No Notes Available

                        </h3>

                        <p class="mb-0">

                            No active study notes are currently available
                            under this chapter.

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