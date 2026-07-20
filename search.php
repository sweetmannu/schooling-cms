<?php

require_once __DIR__ . '/includes/db.php';

$q = '';
$results = [];
$searchError = '';

/*
|--------------------------------------------------------------------------
| Read and validate search keyword
|--------------------------------------------------------------------------
*/

$rawQuery = $_GET['q'] ?? '';

if (is_string($rawQuery)) {

    $q = trim(strip_tags($rawQuery));

    /*
    | Multiple spaces ko single space me convert karega.
    */

    $q = preg_replace('/\s+/u', ' ', $q) ?? '';

    /*
    | Search keyword ki maximum length.
    */

    if (mb_strlen($q, 'UTF-8') > 100) {

        $q = mb_substr(
            $q,
            0,
            100,
            'UTF-8'
        );

    }

}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

if ($q !== '') {

    if (mb_strlen($q, 'UTF-8') < 2) {

        $searchError = 'Please enter at least 2 characters.';

    } else {

        /*
        | Escape LIKE wildcards so that % and _ are treated as text.
        */

        $escapedQuery = str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $q
        );

        $searchTerm = '%' . $escapedQuery . '%';
        $prefixTerm = $escapedQuery . '%';

        $stmt = $pdo->prepare("
            SELECT
                notes.id,
                notes.title,
                notes.slug,
                notes.short_description,

                chapters.chapter_name,

                subjects.subject_name,

                categories.category_name

            FROM notes

            INNER JOIN chapters
                ON chapters.id = notes.chapter_id

            INNER JOIN subjects
                ON subjects.id = chapters.subject_id

            INNER JOIN categories
                ON categories.id = subjects.category_id

            WHERE notes.status = 'Active'
              AND chapters.status = 'Active'
              AND subjects.status = 'Active'
              AND categories.status = 'Active'

              AND (
                    notes.title LIKE ? ESCAPE '\\\\'
                    OR notes.short_description LIKE ? ESCAPE '\\\\'
                    OR chapters.chapter_name LIKE ? ESCAPE '\\\\'
                    OR subjects.subject_name LIKE ? ESCAPE '\\\\'
                    OR categories.category_name LIKE ? ESCAPE '\\\\'
              )

            ORDER BY
                CASE
                    WHEN notes.title = ? THEN 1
                    WHEN notes.title LIKE ? ESCAPE '\\\\' THEN 2
                    WHEN subjects.subject_name LIKE ? ESCAPE '\\\\' THEN 3
                    ELSE 4
                END,

                notes.title ASC

            LIMIT 60
        ");

        $stmt->execute([
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,

            $q,
            $prefixTerm,
            $prefixTerm
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

}

/*
|--------------------------------------------------------------------------
| Page Information
|--------------------------------------------------------------------------
*/

$pageTitle = $q !== ''
    ? 'Search: ' . $q . ' | ' . APP_NAME
    : 'Search | ' . APP_NAME;

$pageDescription = $q !== ''
    ? 'Search results for ' . $q
    : 'Search study notes, subjects and chapters.';

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';

?>

<div class="container py-5">

    <div class="row justify-content-center mb-4">

        <div class="col-lg-8">

            <h1 class="fw-bold text-center mb-4">

                <i class="fa fa-search me-2"></i>

                Search Notes

            </h1>

            <form
                action="<?= htmlspecialchars(
                    APP_URL . '/search.php',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                method="GET">

                <div class="input-group input-group-lg shadow-sm">

                    <input
                        type="search"
                        name="q"
                        class="form-control"
                        placeholder="Search notes, subjects or chapters..."
                        value="<?= htmlspecialchars(
                            $q,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        maxlength="100"
                        required>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fa fa-search me-1"></i>

                        Search

                    </button>

                </div>

            </form>

        </div>

    </div>

    <?php if ($searchError !== '') { ?>

        <div class="alert alert-warning text-center">

            <?= htmlspecialchars(
                $searchError,
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </div>

    <?php } elseif ($q === '') { ?>

        <div class="alert alert-info text-center">

            Please enter a keyword to search.

        </div>

    <?php } else { ?>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <p class="mb-0">

                Showing results for:

                <strong>
                    <?= htmlspecialchars(
                        $q,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>

            </p>

            <span class="badge bg-primary">

                <?= count($results); ?>

                <?= count($results) === 1 ? 'Result' : 'Results'; ?>

            </span>

        </div>

    <?php } ?>

    <?php if ($searchError === '' && count($results) > 0) { ?>

        <div class="row">

            <?php foreach ($results as $item) { ?>

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold">

                                <?= htmlspecialchars(
                                    $item['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </h5>

                            <p class="text-muted small mb-3">

                                <strong>Category:</strong>

                                <?= htmlspecialchars(
                                    $item['category_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                                <br>

                                <strong>Subject:</strong>

                                <?= htmlspecialchars(
                                    $item['subject_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                                <br>

                                <strong>Chapter:</strong>

                                <?= htmlspecialchars(
                                    $item['chapter_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </p>

                            <?php if (!empty($item['short_description'])) { ?>

                                <p class="small text-muted">

                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            strip_tags(
                                                $item['short_description']
                                            ),
                                            0,
                                            140,
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
                                        APP_URL
                                        . '/note/'
                                        . rawurlencode($item['slug']),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="btn btn-primary btn-sm">

                                    <i class="fa fa-book-open me-1"></i>

                                    Read Note

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } elseif (
        $q !== '' &&
        $searchError === '' &&
        count($results) === 0
    ) { ?>

        <div class="alert alert-warning text-center py-4">

            <h5 class="mb-2">

                <i class="fa fa-circle-exclamation me-1"></i>

                No Results Found

            </h5>

            <p class="mb-0">

                Try another keyword or check the spelling.

            </p>

        </div>

    <?php } ?>

</div>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>