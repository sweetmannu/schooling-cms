<?php

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| Extract and Validate YouTube Video ID
|--------------------------------------------------------------------------
*/

function getYoutubeVideoId(string $url): string
{
    if ($url === '') {
        return '';
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }

    $parts = parse_url($url);

    if (!is_array($parts)) {
        return '';
    }

    $host = strtolower($parts['host'] ?? '');
    $path = $parts['path'] ?? '';

    $allowedHosts = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtu.be',
        'www.youtu.be'
    ];

    if (!in_array($host, $allowedHosts, true)) {
        return '';
    }

    $videoId = '';

    /*
    | youtu.be/VIDEO_ID
    */

    if (
        $host === 'youtu.be' ||
        $host === 'www.youtu.be'
    ) {
        $videoId = trim($path, '/');
    }

    /*
    | youtube.com/watch?v=VIDEO_ID
    */

    if (
        $videoId === '' &&
        !empty($parts['query'])
    ) {
        parse_str($parts['query'], $query);

        if (!empty($query['v']) && is_string($query['v'])) {
            $videoId = $query['v'];
        }
    }

    /*
    | youtube.com/embed/VIDEO_ID
    | youtube.com/shorts/VIDEO_ID
    */

    if (
        $videoId === '' &&
        preg_match(
            '#/(?:embed|shorts)/([a-zA-Z0-9_-]{6,20})#',
            $path,
            $matches
        )
    ) {
        $videoId = $matches[1];
    }

    /*
    | YouTube IDs normally contain letters, numbers,
    | underscore and hyphen.
    */

    if (!preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $videoId)) {
        return '';
    }

    return $videoId;
}

/*
|--------------------------------------------------------------------------
| Validate Note Slug
|--------------------------------------------------------------------------
*/

$rawSlug = $_GET['slug'] ?? '';

if (
    !is_string($rawSlug) ||
    trim($rawSlug) === ''
) {
    http_response_code(404);

    $pageTitle = 'Note Not Found | ' . APP_NAME;
    $pageDescription = 'The requested note could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Note Not Found
            </h1>

            <p class="mb-0">
                The requested note is invalid or unavailable.
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

    $pageTitle = 'Note Not Found | ' . APP_NAME;
    $pageDescription = 'The requested note could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Note Not Found
            </h1>

            <p class="mb-0">
                The requested note slug is invalid.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Active Note and Parent Records
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        notes.id,
        notes.chapter_id,
        notes.title,
        notes.slug,
        notes.short_description,
        notes.content,
        notes.pdf_file,
        notes.thumbnail,
        notes.youtube_url,
        notes.meta_title,
        notes.meta_description,
        notes.created_at,
        notes.updated_at,

        chapters.chapter_name,
        chapters.slug AS chapter_slug,

        subjects.subject_name,
        subjects.slug AS subject_slug,

        categories.category_name,
        categories.slug AS category_slug

    FROM notes

    INNER JOIN chapters
        ON chapters.id = notes.chapter_id

    INNER JOIN subjects
        ON subjects.id = chapters.subject_id

    INNER JOIN categories
        ON categories.id = subjects.category_id

    WHERE notes.slug = ?
      AND notes.status = 'Active'
      AND chapters.status = 'Active'
      AND subjects.status = 'Active'
      AND categories.status = 'Active'

    LIMIT 1
");

$stmt->execute([$slug]);

$note = $stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Note Not Found
|--------------------------------------------------------------------------
*/

if (!$note) {
    http_response_code(404);

    $pageTitle = 'Note Not Found | ' . APP_NAME;
    $pageDescription = 'The requested note could not be found.';
    $robotsMeta = 'noindex,follow';

    require_once __DIR__ . '/includes/frontend/header.php';
    require_once __DIR__ . '/includes/frontend/navbar.php';

    ?>

    <main class="container py-5">

        <div class="alert alert-danger text-center py-4">

            <h1 class="h4 mb-2">
                Note Not Found
            </h1>

            <p class="mb-0">
                This note does not exist or is currently unavailable.
            </p>

        </div>

    </main>

    <?php

    require_once __DIR__ . '/includes/frontend/footer.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| Related Notes
|--------------------------------------------------------------------------
*/

$relatedStmt = $pdo->prepare("
    SELECT
        id,
        title,
        slug,
        short_description,
        thumbnail
    FROM notes
    WHERE chapter_id = ?
      AND id != ?
      AND status = 'Active'
    ORDER BY updated_at DESC, id DESC
    LIMIT 4
");

$relatedStmt->execute([
    $note['chapter_id'],
    $note['id']
]);

$relatedNotes = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| YouTube Video
|--------------------------------------------------------------------------
*/

$videoId = getYoutubeVideoId(
    trim((string)($note['youtube_url'] ?? ''))
);

/*
|--------------------------------------------------------------------------
| Page SEO
|--------------------------------------------------------------------------
*/

$pageTitle = !empty($note['meta_title'])
    ? $note['meta_title']
    : $note['title'] . ' | ' . APP_NAME;

$pageDescription = !empty($note['meta_description'])
    ? $note['meta_description']
    : (
        !empty($note['short_description'])
            ? mb_strimwidth(
                strip_tags($note['short_description']),
                0,
                160,
                '...',
                'UTF-8'
            )
            : 'Read study notes for ' . $note['title'] . '.'
    );

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';
require_once __DIR__ . '/includes/frontend/breadcrumb.php';

?>

<main class="container py-5">

    <article class="card shadow border-0 rounded-4">

        <div class="card-body p-4 p-lg-5">

            <!-- Note Title -->

            <header class="mb-4">

                <h1 class="display-5 fw-bold mb-3">

                    <?= htmlspecialchars(
                        $note['title'],
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </h1>

                <!-- Note Information -->

                <div class="row g-3">

                    <div class="col-md-6 col-lg-3">

                        <div class="border rounded-3 p-3 bg-light h-100">

                            <small class="text-muted d-block mb-1">
                                Category
                            </small>

                            <strong>

                                <?= htmlspecialchars(
                                    $note['category_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6 col-lg-3">

                        <div class="border rounded-3 p-3 bg-light h-100">

                            <small class="text-muted d-block mb-1">
                                Subject
                            </small>

                            <strong>

                                <?= htmlspecialchars(
                                    $note['subject_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6 col-lg-3">

                        <div class="border rounded-3 p-3 bg-light h-100">

                            <small class="text-muted d-block mb-1">
                                Chapter
                            </small>

                            <strong>

                                <?= htmlspecialchars(
                                    $note['chapter_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </strong>

                        </div>

                    </div>

                    <div class="col-md-6 col-lg-3">

                        <div class="border rounded-3 p-3 bg-light h-100">

                            <small class="text-muted d-block mb-1">
                                Published
                            </small>

                            <strong>

                                <?php
                                $createdTimestamp = !empty(
                                    $note['created_at']
                                )
                                    ? strtotime($note['created_at'])
                                    : false;
                                ?>

                                <?= $createdTimestamp !== false
                                    ? htmlspecialchars(
                                        date(
                                            'd M Y',
                                            $createdTimestamp
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                    : 'Not Available'; ?>

                            </strong>

                        </div>

                    </div>

                </div>

                <?php

                $createdTimestamp = !empty($note['created_at'])
                    ? strtotime($note['created_at'])
                    : false;

                $updatedTimestamp = !empty($note['updated_at'])
                    ? strtotime($note['updated_at'])
                    : false;

                $showUpdatedDate =
                    $updatedTimestamp !== false &&
                    (
                        $createdTimestamp === false ||
                        $updatedTimestamp > $createdTimestamp
                    );

                ?>

                <?php if ($showUpdatedDate) { ?>

                    <p class="text-muted small mt-3 mb-0">

                        <i
                            class="fa fa-clock me-1"
                            aria-hidden="true"></i>

                        Last updated:

                        <?= htmlspecialchars(
                            date(
                                'd M Y, h:i A',
                                $updatedTimestamp
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </p>

                <?php } ?>

            </header>

            <!-- Thumbnail -->

            <?php if (!empty($note['thumbnail'])) { ?>

                <div class="text-center mb-4">

                    <img
                        src="<?= htmlspecialchars(
                            rtrim(APP_URL, '/')
                            . '/uploads/thumbnails/'
                            . rawurlencode($note['thumbnail']),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        alt="<?= htmlspecialchars(
                            $note['title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        class="img-fluid rounded-3 shadow-sm"
                        style="max-height:350px;"
                        loading="lazy">

                </div>

            <?php } ?>

            <!-- Short Description -->

            <?php if (!empty($note['short_description'])) { ?>

                <div class="alert alert-info">

                    <?= nl2br(
                        htmlspecialchars(
                            $note['short_description'],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ); ?>

                </div>

            <?php } ?>

            <!-- PDF -->

            <?php if (!empty($note['pdf_file'])) { ?>

                <div class="mb-4">

                    <a
                        href="<?= htmlspecialchars(
                            rtrim(APP_URL, '/')
                            . '/uploads/pdf/'
                            . rawurlencode($note['pdf_file']),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-danger">

                        <i
                            class="fa fa-file-pdf me-1"
                            aria-hidden="true"></i>

                        View PDF

                    </a>

                </div>

            <?php } ?>

            <!-- YouTube Video -->

            <?php if ($videoId !== '') { ?>

                <section class="mb-4">

                    <h2 class="h4 mb-3">

                        <i
                            class="fa-brands fa-youtube text-danger me-2"
                            aria-hidden="true"></i>

                        Watch Video

                    </h2>

                    <div class="ratio ratio-16x9">

                        <iframe
                            src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars(
                                $videoId,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            title="<?= htmlspecialchars(
                                'Video for ' . $note['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>

                    </div>

                </section>

            <?php } ?>

            <hr>

            <!-- Main Content -->

            <section
                class="note-content mb-5"
                aria-label="Note content">

                <?php
                /*
                | Content is intentionally rendered as HTML because it is
                | created by authenticated administrators through TinyMCE.
                | Only trusted administrators should receive editing access.
                */
                ?>

                <?= $note['content']; ?>

            </section>

            <!-- Related Notes -->

            <?php if (count($relatedNotes) > 0) { ?>

                <hr>

                <section aria-labelledby="related-notes-heading">

                    <h2
                        id="related-notes-heading"
                        class="h3 fw-bold mb-4">

                        Related Notes

                    </h2>

                    <div class="row">

                        <?php foreach ($relatedNotes as $item) { ?>

                            <div class="col-md-6 col-lg-3 mb-4">

                                <article
                                    class="card h-100 shadow-sm border-0">

                                    <?php if (!empty($item['thumbnail'])) { ?>

                                        <img
                                            src="<?= htmlspecialchars(
                                                rtrim(APP_URL, '/')
                                                . '/uploads/thumbnails/'
                                                . rawurlencode(
                                                    $item['thumbnail']
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                            alt="<?= htmlspecialchars(
                                                $item['title'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                            class="card-img-top"
                                            style="height:150px; object-fit:cover;"
                                            loading="lazy">

                                    <?php } ?>

                                    <div class="card-body d-flex flex-column">

                                        <h3 class="h5">

                                            <?= htmlspecialchars(
                                                $item['title'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </h3>

                                        <?php if (
                                            !empty(
                                                $item['short_description']
                                            )
                                        ) { ?>

                                            <p class="small text-muted">

                                                <?= htmlspecialchars(
                                                    mb_strimwidth(
                                                        strip_tags(
                                                            $item[
                                                                'short_description'
                                                            ]
                                                        ),
                                                        0,
                                                        90,
                                                        '...',
                                                        'UTF-8'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>

                                            </p>

                                        <?php } ?>

                                        <div class="mt-auto">

                                            <a
                                                href="<?= htmlspecialchars(
                                                    rtrim(APP_URL, '/')
                                                    . '/note/'
                                                    . rawurlencode(
                                                        $item['slug']
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

                    </div>

                </section>

            <?php } ?>

        </div>

    </article>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>