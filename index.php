<?php

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| Homepage SEO
|--------------------------------------------------------------------------
*/

$pageTitle = APP_NAME . ' | Free Study Notes';

$pageDescription =
    'Browse free study notes, subjects, chapters and educational resources.';

/*
|--------------------------------------------------------------------------
| Active Main Categories
|--------------------------------------------------------------------------
*/

$categoryStmt = $pdo->query("
    SELECT
        id,
        category_name,
        slug
    FROM categories
    WHERE status = 'Active'
      AND parent_id IS NULL
    ORDER BY category_name ASC
");

    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    /*
|--------------------------------------------------------------------------
| Popular Subjects
|--------------------------------------------------------------------------
|
| Active subjects from active categories.
| Subjects with more chapters appear first.
|
|--------------------------------------------------------------------------
*/

$popularSubjectStmt = $pdo->query("
    SELECT
        s.id,
        s.subject_name,
        s.slug,
        s.icon,
        c.category_name,
        COUNT(ch.id) AS chapter_count

    FROM subjects s

    INNER JOIN categories c
        ON c.id = s.category_id

    LEFT JOIN chapters ch
        ON ch.subject_id = s.id
        AND ch.status = 'Active'

    WHERE s.status = 'Active'
      AND c.status = 'Active'

    GROUP BY
        s.id,
        s.subject_name,
        s.slug,
        s.icon,
        c.category_name

    ORDER BY
        chapter_count DESC,
        s.subject_name ASC

    LIMIT 8
");

$popularSubjects =
    $popularSubjectStmt->fetchAll(PDO::FETCH_ASSOC);


    /*
|--------------------------------------------------------------------------
| Homepage Statistics
|--------------------------------------------------------------------------
*/

$statsStmt = $pdo->query("
    SELECT

        (
            SELECT COUNT(*)
            FROM categories
            WHERE status = 'Active'
              AND parent_id IS NULL
        ) AS total_classes,

        (
            SELECT COUNT(*)
            FROM subjects
            WHERE status = 'Active'
        ) AS total_subjects,

        (
            SELECT COUNT(*)
            FROM chapters
            WHERE status = 'Active'
        ) AS total_chapters,

        (
            SELECT COUNT(*)
            FROM notes
            WHERE status = 'Active'
        ) AS total_notes
");

$homepageStats = $statsStmt->fetch(PDO::FETCH_ASSOC);

$totalClasses =
    (int) ($homepageStats['total_classes'] ?? 0);

$totalSubjects =
    (int) ($homepageStats['total_subjects'] ?? 0);

$totalChapters =
    (int) ($homepageStats['total_chapters'] ?? 0);

$totalNotes =
    (int) ($homepageStats['total_notes'] ?? 0);

/*
|--------------------------------------------------------------------------
| Featured Notes
|--------------------------------------------------------------------------
*/

$featuredStmt = $pdo->query("
    SELECT
        notes.id,
        notes.title,
        notes.slug,
        notes.short_description,
        notes.thumbnail,

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
      AND notes.featured = 'Yes'
      AND chapters.status = 'Active'
      AND subjects.status = 'Active'
      AND categories.status = 'Active'

    ORDER BY notes.updated_at DESC, notes.id DESC

    LIMIT 6
");

$featuredNotes = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Latest Notes
|--------------------------------------------------------------------------
*/

$latestStmt = $pdo->query("
    SELECT
        notes.id,
        notes.title,
        notes.slug,
        notes.short_description,
        notes.thumbnail,
        notes.created_at,

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

    ORDER BY notes.created_at DESC, notes.id DESC

    LIMIT 8
");

$latestNotes = $latestStmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';


$heroSiteName = setting(
    'site_name',
    APP_NAME
);

$heroSiteLogo = setting(
    'site_logo',
    ''
);

$heroLogoUrl = '';

if (!empty($heroSiteLogo)) {

    $heroLogoUrl =
        rtrim(APP_URL, '/')
        . '/uploads/logo/'
        . rawurlencode(
            basename($heroSiteLogo)
        );
}

?>


<main>

<!-- ==========================================================
     Hero Section
=========================================================== -->

<section class="home-hero">

    <div class="container">

        <div class="row align-items-center g-5">

            <!-- Hero Content -->

            <div class="col-lg-7">

                <div class="hero-content">

                    <span class="hero-badge">

                        <i
                            class="fa-solid fa-graduation-cap me-2"
                            aria-hidden="true">
                        </i>

                        Learn • Practice • Grow

                    </span>

                    <h1 class="hero-title">

                        Learn Better.
                        <span>Practice Smarter.</span>
                        Grow Every Day.

                    </h1>

                    <p class="hero-description">

                        Free study notes, chapters, PDFs and learning
                        resources organized by class and subject to help
                        students learn with confidence.

                    </p>


                    <!-- Hero Search -->

                    <form
                        action="<?= htmlspecialchars(
                            rtrim(APP_URL, '/') . '/search.php',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        method="GET"
                        role="search"
                        class="hero-search-form"
                    >

                        <label
                            for="hero-search"
                            class="visually-hidden"
                        >
                            Search study resources
                        </label>

                        <div class="hero-search-box">

                            <span
                                class="hero-search-icon"
                                aria-hidden="true"
                            >

                                <i
                                    class="fa-solid fa-magnifying-glass"
                                ></i>

                            </span>

                            <input
                                id="hero-search"
                                type="search"
                                name="q"
                                class="form-control"
                                placeholder="Search notes, subjects, chapters..."
                                maxlength="100"
                                autocomplete="off"
                                required
                            >

                            <button
                                type="submit"
                                class="btn btn-warning hero-search-button"
                            >

                                Search

                            </button>

                        </div>

                    </form>


                    <!-- Hero Actions -->

                    <div
                        class="hero-actions d-flex flex-wrap gap-3"
                    >

                        <a
                            href="#browse-classes"
                            class="btn btn-light btn-lg"
                        >

                            <i
                                class="fa-solid fa-layer-group me-2"
                                aria-hidden="true"
                            ></i>

                            Browse Classes

                        </a>

                        <a
                            href="#latest-notes"
                            class="btn btn-outline-light btn-lg"
                        >

                            <i
                                class="fa-solid fa-book-open me-2"
                                aria-hidden="true"
                            ></i>

                            Explore Latest Notes

                        </a>

                    </div>

                </div>

            </div>


            <!-- Hero Visual -->

            <div class="col-lg-5">

                <div
                    class="hero-visual"
                    aria-hidden="true"
                >

                    <div class="hero-visual-glow"></div>


                    <!-- Main Learning Card -->

                    <div class="hero-learning-card">

                        <div class="hero-learning-icon">

                            <i class="fa-solid fa-book-open"></i>

                        </div>

                        <div>

                            <span>
                                Start Learning
                            </span>

                            <strong>
                                Study Smarter
                            </strong>

                        </div>

                    </div>


                    <!-- Decorative Subject Cards -->

                    <div class="hero-subject-card hero-subject-one">

                        <i class="fa-solid fa-calculator"></i>

                        <span>
                            Maths
                        </span>

                    </div>

                    <div class="hero-subject-card hero-subject-two">

                        <i class="fa-solid fa-flask"></i>

                        <span>
                            Science
                        </span>

                    </div>

                    <div class="hero-subject-card hero-subject-three">

                        <i class="fa-solid fa-book"></i>

                        <span>
                            Notes
                        </span>

                    </div>


                    <!-- Central Illustration -->

                    <div class="hero-main-illustration">

    <div class="hero-logo-wrap">

        <?php if ($heroLogoUrl !== '') { ?>

            <img
                src="<?= htmlspecialchars(
                    $heroLogoUrl,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                alt="<?= htmlspecialchars(
                    $heroSiteName,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
                class="hero-brand-logo"
            >

        <?php } else { ?>

            <i
                class="fa-solid fa-graduation-cap"
                aria-hidden="true"
            ></i>

        <?php } ?>

    </div>

</div>
                </div>

            </div>

        </div>

    </div>

</section>

        <!-- ==========================================================
     Browse by Class
=========================================================== -->

<section
    id="browse-classes"
    class="home-section browse-classes-section"
>

    <div class="container">

        <div class="section-heading-row">

            <div>

                <span class="section-kicker">
                    Explore Learning
                </span>

                <h2 class="home-section-title">

                    <i
                        class="fa-solid fa-graduation-cap text-primary me-2"
                        aria-hidden="true"
                    ></i>

                    Browse by Class

                </h2>

                <p class="home-section-subtitle">

                    Choose your class to explore subjects,
                    chapters and study notes.

                </p>

            </div>

        </div>


        <?php if (!empty($categories)) { ?>

            <div class="class-grid">

                <?php

                $classIcons = [
                    'fa-pencil',
                    'fa-book-open',
                    'fa-calculator',
                    'fa-flask',
                    'fa-earth-asia',
                    'fa-laptop',
                    'fa-compass-drafting',
                    'fa-atom',
                    'fa-microscope',
                    'fa-bullseye'
                ];

                $classColors = [
                    'class-tone-1',
                    'class-tone-2',
                    'class-tone-3',
                    'class-tone-4',
                    'class-tone-5',
                    'class-tone-6',
                    'class-tone-7',
                    'class-tone-8',
                    'class-tone-9',
                    'class-tone-10'
                ];

                ?>

                <?php foreach ($categories as $index => $category) { ?>

                    <?php

                    $iconClass =
                        $classIcons[
                            $index % count($classIcons)
                        ];

                    $toneClass =
                        $classColors[
                            $index % count($classColors)
                        ];

                    $categoryUrl =
                        rtrim(APP_URL, '/')
                        . '/category/'
                        . rawurlencode(
                            $category['slug']
                        );

                    ?>

                    <a
                        href="<?= htmlspecialchars(
                            $categoryUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        class="class-card <?= htmlspecialchars(
                            $toneClass,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                        <span
                            class="class-card-icon"
                            aria-hidden="true"
                        >

                            <i
                                class="fa-solid <?= htmlspecialchars(
                                    $iconClass,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                            ></i>

                        </span>

                        <span class="class-card-title">

                            <?= htmlspecialchars(
                                $category['category_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </span>

                        <span class="class-card-link">

                            View Subjects

                            <i
                                class="fa-solid fa-arrow-right ms-1"
                                aria-hidden="true"
                            ></i>

                        </span>

                    </a>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="empty-state-card">

                <i
                    class="fa-solid fa-folder-open"
                    aria-hidden="true"
                ></i>

                <h3>
                    No Classes Available
                </h3>

                <p>
                    Classes will appear here once they are added
                    and activated from the admin panel.
                </p>

            </div>

        <?php } ?>

    </div>

</section>

<!-- ==========================================================
     Popular Subjects
=========================================================== -->

<section
    id="popular-subjects"
    class="home-section popular-subjects-section"
>

    <div class="container">

        <div class="section-heading-row">

            <div>

                <span class="section-kicker">
                    Start Studying
                </span>

                <h2 class="home-section-title">

                    <i
                        class="fa-solid fa-book-open text-primary me-2"
                        aria-hidden="true"
                    ></i>

                    Popular Subjects

                </h2>

                <p class="home-section-subtitle">

                    Explore popular subjects and quickly access
                    their chapters and study resources.

                </p>

            </div>

        </div>


        <?php if (!empty($popularSubjects)) { ?>

            <div class="subject-grid">

                <?php

                $defaultSubjectIcons = [
                    'fa-calculator',
                    'fa-flask',
                    'fa-language',
                    'fa-earth-asia',
                    'fa-computer',
                    'fa-book',
                    'fa-atom',
                    'fa-vial'
                ];

                ?>

                <?php foreach (
                    $popularSubjects as $index => $subject
                ) { ?>

                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | Subject Icon
                    |--------------------------------------------------------------------------
                    |
                    | DB icon available ho to use karenge.
                    | Otherwise safe default icon cycle hoga.
                    |
                    */

                    $subjectIcon =
                        trim(
                            (string) (
                                $subject['icon'] ?? ''
                            )
                        );

                    if ($subjectIcon === '') {

                        $subjectIcon =
                            $defaultSubjectIcons[
                                $index
                                % count(
                                    $defaultSubjectIcons
                                )
                            ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Normalize Font Awesome Class
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !str_starts_with(
                            $subjectIcon,
                            'fa-'
                        )
                    ) {

                        $subjectIcon = 'fa-book';
                    }

                    $subjectUrl =
                        rtrim(APP_URL, '/')
                        . '/subject/'
                        . rawurlencode(
                            $subject['slug']
                        );

                    ?>

                    <a
                        href="<?= htmlspecialchars(
                            $subjectUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        class="subject-card"
                    >

                        <span
                            class="subject-card-icon subject-icon-<?= (
                                $index % 8
                            ) + 1; ?>"
                            aria-hidden="true"
                        >

                            <i
                                class="fa-solid <?= htmlspecialchars(
                                    $subjectIcon,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                            ></i>

                        </span>


                        <span class="subject-card-content">

                            <strong class="subject-card-title">

                                <?= htmlspecialchars(
                                    $subject['subject_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </strong>

                            <span class="subject-card-category">

                                <?= htmlspecialchars(
                                    $subject['category_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>

                            <span class="subject-card-meta">

                                <?= (int) $subject[
                                    'chapter_count'
                                ]; ?>

                                <?= (int) $subject[
                                    'chapter_count'
                                ] === 1
                                    ? 'Chapter'
                                    : 'Chapters'; ?>

                            </span>

                        </span>


                        <span
                            class="subject-card-arrow"
                            aria-hidden="true"
                        >

                            <i
                                class="fa-solid fa-arrow-right"
                            ></i>

                        </span>

                    </a>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="empty-state-card">

                <i
                    class="fa-solid fa-book-open"
                    aria-hidden="true"
                ></i>

                <h3>
                    No Subjects Available
                </h3>

                <p>

                    Subjects will appear here once they are added
                    and activated from the admin panel.

                </p>

            </div>

        <?php } ?>

    </div>

</section>

        <?php if (!empty($featuredNotes)) { ?>

<!-- ==========================================================
     Featured Notes
=========================================================== -->

<section
    id="featured-notes"
    class="home-section featured-notes-section"
>

    <div class="container">

        <div class="section-heading-row">

            <div>

                <span class="section-kicker">
                    Handpicked Resources
                </span>

                <h2 class="home-section-title">

                    <i
                        class="fa-solid fa-star text-warning me-2"
                        aria-hidden="true"
                    ></i>

                    Featured Notes

                </h2>

                <p class="home-section-subtitle">
                    Explore highlighted study notes selected for quick learning.
                </p>

            </div>

        </div>


        <div class="featured-notes-grid">

            <?php foreach ($featuredNotes as $note) { ?>

                <?php

                $noteUrl =
                    rtrim(APP_URL, '/')
                    . '/note/'
                    . rawurlencode(
                        $note['slug']
                    );

                $thumbnailUrl = '';

                if (!empty($note['thumbnail'])) {

                    $thumbnailUrl =
                        rtrim(APP_URL, '/')
                        . '/uploads/thumbnails/'
                        . rawurlencode(
                            basename(
                                $note['thumbnail']
                            )
                        );
                }

                ?>

                <article class="featured-note-card">

                    <a
                        href="<?= htmlspecialchars(
                            $noteUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        class="featured-note-image-link"
                    >

                        <?php if ($thumbnailUrl !== '') { ?>

                            <img
                                src="<?= htmlspecialchars(
                                    $thumbnailUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                alt="<?= htmlspecialchars(
                                    $note['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                class="featured-note-image"
                                loading="lazy"
                            >

                        <?php } else { ?>

                            <div class="featured-note-placeholder">

                                <i
                                    class="fa-solid fa-book-open"
                                    aria-hidden="true"
                                ></i>

                            </div>

                        <?php } ?>

                        <span class="featured-note-badge">

                            <i
                                class="fa-solid fa-star me-1"
                                aria-hidden="true"
                            ></i>

                            Featured

                        </span>

                    </a>


                    <div class="featured-note-body">

                        <div class="featured-note-meta">

                            <span>

                                <?= htmlspecialchars(
                                    $note['category_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>

                            <span aria-hidden="true">
                                •
                            </span>

                            <span>

                                <?= htmlspecialchars(
                                    $note['subject_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>

                        </div>


                        <h3 class="featured-note-title">

                            <a
                                href="<?= htmlspecialchars(
                                    $noteUrl,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                            >

                                <?= htmlspecialchars(
                                    $note['title'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </a>

                        </h3>


                        <p class="featured-note-chapter">

                            <i
                                class="fa-solid fa-bookmark me-1"
                                aria-hidden="true"
                            ></i>

                            <?= htmlspecialchars(
                                $note['chapter_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </p>


                        <?php if (
                            !empty(
                                $note['short_description']
                            )
                        ) { ?>

                            <p class="featured-note-description">

                                <?= htmlspecialchars(
                                    mb_strimwidth(
                                        strip_tags(
                                            $note[
                                                'short_description'
                                            ]
                                        ),
                                        0,
                                        120,
                                        '...',
                                        'UTF-8'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </p>

                        <?php } ?>


                        <a
                            href="<?= htmlspecialchars(
                                $noteUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="featured-note-link"
                        >

                            Read Note

                            <i
                                class="fa-solid fa-arrow-right ms-1"
                                aria-hidden="true"
                            ></i>

                        </a>

                    </div>

                </article>

            <?php } ?>

        </div>

    </div>

</section>

<?php } ?>

        <!-- ==========================================================
     Latest Study Notes
=========================================================== -->

<section
    id="latest-notes"
    class="home-section latest-notes-section"
>

    <div class="container">

        <div class="section-heading-row">

            <div>

                <span class="section-kicker">
                    Recently Added
                </span>

                <h2 class="home-section-title">

                    <i
                        class="fa-solid fa-clock text-primary me-2"
                        aria-hidden="true"
                    ></i>

                    Latest Study Notes

                </h2>

                <p class="home-section-subtitle">

                    Discover recently added notes and learning resources.

                </p>

            </div>

        </div>


        <?php if (!empty($latestNotes)) { ?>

            <div class="latest-notes-grid">

                <?php foreach ($latestNotes as $note) { ?>

                    <?php

                    $noteUrl =
                        rtrim(APP_URL, '/')
                        . '/note/'
                        . rawurlencode(
                            $note['slug']
                        );

                    $thumbnailUrl = '';

                    if (!empty($note['thumbnail'])) {

                        $thumbnailUrl =
                            rtrim(APP_URL, '/')
                            . '/uploads/thumbnails/'
                            . rawurlencode(
                                basename(
                                    $note['thumbnail']
                                )
                            );
                    }

                    ?>

                    <article class="latest-note-card">

                        <a
                            href="<?= htmlspecialchars(
                                $noteUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="latest-note-image-link"
                        >

                            <?php if ($thumbnailUrl !== '') { ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $thumbnailUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    alt="<?= htmlspecialchars(
                                        $note['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="latest-note-image"
                                    loading="lazy"
                                >

                            <?php } else { ?>

                                <div class="latest-note-placeholder">

                                    <i
                                        class="fa-solid fa-book-open"
                                        aria-hidden="true"
                                    ></i>

                                </div>

                            <?php } ?>

                        </a>


                        <div class="latest-note-body">

                            <div class="latest-note-meta">

                                <span>

                                    <?= htmlspecialchars(
                                        $note['category_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </span>

                                <span aria-hidden="true">
                                    •
                                </span>

                                <span>

                                    <?= htmlspecialchars(
                                        $note['subject_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </span>

                            </div>


                            <h3 class="latest-note-title">

                                <a
                                    href="<?= htmlspecialchars(
                                        $noteUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >

                                    <?= htmlspecialchars(
                                        $note['title'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </a>

                            </h3>


                            <p class="latest-note-chapter">

                                <i
                                    class="fa-solid fa-bookmark me-1"
                                    aria-hidden="true"
                                ></i>

                                <?= htmlspecialchars(
                                    $note['chapter_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </p>


                            <?php if (
                                !empty($note['created_at'])
                            ) { ?>

                                <p class="latest-note-date">

                                    <i
                                        class="fa-solid fa-calendar-days me-1"
                                        aria-hidden="true"
                                    ></i>

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


                            <?php if (
                                !empty(
                                    $note['short_description']
                                )
                            ) { ?>

                                <p class="latest-note-description">

                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            strip_tags(
                                                $note[
                                                    'short_description'
                                                ]
                                            ),
                                            0,
                                            105,
                                            '...',
                                            'UTF-8'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </p>

                            <?php } ?>


                            <div class="latest-note-footer">

                                <a
                                    href="<?= htmlspecialchars(
                                        $noteUrl,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                    class="latest-note-link"
                                >

                                    Read Note

                                    <i
                                        class="fa-solid fa-arrow-right ms-1"
                                        aria-hidden="true"
                                    ></i>

                                </a>

                            </div>

                        </div>

                    </article>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="empty-state-card">

                <i
                    class="fa-solid fa-book-open"
                    aria-hidden="true"
                ></i>

                <h3>
                    No Notes Available
                </h3>

                <p>

                    New study notes will appear here once they are published.

                </p>

            </div>

        <?php } ?>

    </div>

</section>

<!-- ==========================================================
     Learning Statistics
=========================================================== -->

<section class="home-stats-section">

    <div class="container">

        <div class="home-stats-wrap">

            <!-- Classes -->

            <div class="home-stat-item">

                <div class="home-stat-icon">

                    <i
                        class="fa-solid fa-graduation-cap"
                        aria-hidden="true"
                    ></i>

                </div>

                <div>

                    <strong class="home-stat-number">

                        <?= number_format(
                            $totalClasses
                        ); ?>

                    </strong>

                    <span class="home-stat-label">
                        Classes
                    </span>

                </div>

            </div>


            <!-- Subjects -->

            <div class="home-stat-item">

                <div class="home-stat-icon">

                    <i
                        class="fa-solid fa-book-open"
                        aria-hidden="true"
                    ></i>

                </div>

                <div>

                    <strong class="home-stat-number">

                        <?= number_format(
                            $totalSubjects
                        ); ?>

                    </strong>

                    <span class="home-stat-label">
                        Subjects
                    </span>

                </div>

            </div>


            <!-- Chapters -->

            <div class="home-stat-item">

                <div class="home-stat-icon">

                    <i
                        class="fa-solid fa-file-lines"
                        aria-hidden="true"
                    ></i>

                </div>

                <div>

                    <strong class="home-stat-number">

                        <?= number_format(
                            $totalChapters
                        ); ?>

                    </strong>

                    <span class="home-stat-label">
                        Chapters
                    </span>

                </div>

            </div>


            <!-- Notes -->

            <div class="home-stat-item">

                <div class="home-stat-icon">

                    <i
                        class="fa-solid fa-note-sticky"
                        aria-hidden="true"
                    ></i>

                </div>

                <div>

                    <strong class="home-stat-number">

                        <?= number_format(
                            $totalNotes
                        ); ?>

                    </strong>

                    <span class="home-stat-label">
                        Study Notes
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================================
     How Learning Works
=========================================================== -->

<section class="home-section learning-flow-section">

    <div class="container">

        <div class="section-heading-row">

            <div>

                <span class="section-kicker">
                    Simple Learning Journey
                </span>

                <h2 class="home-section-title">

                    <i
                        class="fa-solid fa-route text-primary me-2"
                        aria-hidden="true"
                    ></i>

                    How Learning Works

                </h2>

                <p class="home-section-subtitle">

                    Find the study material you need in just four simple steps.

                </p>

            </div>

        </div>


        <div class="learning-flow">

            <!-- Step 1 -->

            <div class="learning-step">

                <div class="learning-step-icon learning-step-blue">

                    <i
                        class="fa-solid fa-layer-group"
                        aria-hidden="true"
                    ></i>

                </div>

                <div class="learning-step-content">

                    <span class="learning-step-number">
                        Step 1
                    </span>

                    <h3>
                        Choose Class
                    </h3>

                    <p>
                        Select your class from the available learning categories.
                    </p>

                </div>

            </div>


            <div
                class="learning-flow-arrow"
                aria-hidden="true"
            >

                <i class="fa-solid fa-arrow-right"></i>

            </div>


            <!-- Step 2 -->

            <div class="learning-step">

                <div class="learning-step-icon learning-step-green">

                    <i
                        class="fa-solid fa-book-open"
                        aria-hidden="true"
                    ></i>

                </div>

                <div class="learning-step-content">

                    <span class="learning-step-number">
                        Step 2
                    </span>

                    <h3>
                        Choose Subject
                    </h3>

                    <p>
                        Pick the subject you want to study and explore.
                    </p>

                </div>

            </div>


            <div
                class="learning-flow-arrow"
                aria-hidden="true"
            >

                <i class="fa-solid fa-arrow-right"></i>

            </div>


            <!-- Step 3 -->

            <div class="learning-step">

                <div class="learning-step-icon learning-step-orange">

                    <i
                        class="fa-solid fa-file-lines"
                        aria-hidden="true"
                    ></i>

                </div>

                <div class="learning-step-content">

                    <span class="learning-step-number">
                        Step 3
                    </span>

                    <h3>
                        Select Chapter
                    </h3>

                    <p>
                        Open the chapter containing the topic you want to learn.
                    </p>

                </div>

            </div>


            <div
                class="learning-flow-arrow"
                aria-hidden="true"
            >

                <i class="fa-solid fa-arrow-right"></i>

            </div>


            <!-- Step 4 -->

            <div class="learning-step">

                <div class="learning-step-icon learning-step-purple">

                    <i
                        class="fa-solid fa-pen-to-square"
                        aria-hidden="true"
                    ></i>

                </div>

                <div class="learning-step-content">

                    <span class="learning-step-number">
                        Step 4
                    </span>

                    <h3>
                        Read & Learn
                    </h3>

                    <p>
                        Study notes, PDFs and learning resources at your pace.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================================
     Why Choose Keeru Kakshaa
=========================================================== -->

<section class="home-section why-choose-section">

    <div class="container">

        <div class="why-choose-layout">

            <!-- Left Content -->

            <div class="why-choose-intro">

                <span class="section-kicker">
                    Why Keeru Kakshaa
                </span>

                <h2 class="home-section-title">

                    Learning Made
                    <span>Simple & Organized</span>

                </h2>

                <p class="home-section-subtitle">

                    Everything students need to find,
                    understand and access study material
                    without unnecessary complexity.

                </p>

                <div class="why-highlight">

                    <div class="why-highlight-icon">

                        <i
                            class="fa-solid fa-graduation-cap"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <strong>
                            Learn • Practice • Grow
                        </strong>

                        <span>
                            A simple learning experience built
                            around students.
                        </span>

                    </div>

                </div>

            </div>


            <!-- Feature Grid -->

            <div class="why-feature-grid">

                <!-- Feature 1 -->

                <div class="why-feature-card">

                    <div class="why-feature-icon why-icon-blue">

                        <i
                            class="fa-solid fa-layer-group"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h3>
                            Organized Learning
                        </h3>

                        <p>
                            Study resources arranged clearly
                            by class, subject and chapter.
                        </p>

                    </div>

                </div>


                <!-- Feature 2 -->

                <div class="why-feature-card">

                    <div class="why-feature-icon why-icon-green">

                        <i
                            class="fa-solid fa-file-pdf"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h3>
                            Notes & PDFs
                        </h3>

                        <p>
                            Quickly access useful notes,
                            PDFs and chapter resources.
                        </p>

                    </div>

                </div>


                <!-- Feature 3 -->

                <div class="why-feature-card">

                    <div class="why-feature-icon why-icon-orange">

                        <i
                            class="fa-solid fa-magnifying-glass"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h3>
                            Easy to Find
                        </h3>

                        <p>
                            Search and browse learning
                            material without wasting time.
                        </p>

                    </div>

                </div>


                <!-- Feature 4 -->

                <div class="why-feature-card">

                    <div class="why-feature-icon why-icon-purple">

                        <i
                            class="fa-solid fa-mobile-screen-button"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h3>
                            Learn Anywhere
                        </h3>

                        <p>
                            A responsive learning experience
                            across desktop, tablet and mobile.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ==========================================================
     Bottom Search CTA
=========================================================== -->

<section class="home-search-cta-section">

    <div class="container">

        <div class="home-search-cta">

            <!-- Decorative Icon -->

            <div
                class="home-search-cta-visual"
                aria-hidden="true"
            >

                <div class="home-search-cta-icon">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </div>

            </div>


            <!-- Content -->

            <div class="home-search-cta-content">

                <span class="section-kicker">
                    Find What You Need
                </span>

                <h2>
                    What do you want to study today?
                </h2>

                <p>
                    Search notes, subjects, chapters and learning resources
                    from one place.
                </p>


                <form
                    action="<?= htmlspecialchars(
                        rtrim(APP_URL, '/') . '/search.php',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    method="GET"
                    role="search"
                    class="home-search-cta-form"
                >

                    <label
                        for="bottom-search"
                        class="visually-hidden"
                    >
                        Search study resources
                    </label>

                    <div class="home-search-cta-box">

                        <span
                            class="home-search-cta-search-icon"
                            aria-hidden="true"
                        >

                            <i
                                class="fa-solid fa-magnifying-glass"
                            ></i>

                        </span>

                        <input
                            id="bottom-search"
                            type="search"
                            name="q"
                            class="form-control"
                            placeholder="Search notes, chapters, subjects..."
                            maxlength="100"
                            autocomplete="off"
                            required
                        >

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            Search

                        </button>

                    </div>

                </form>

            </div>


            <!-- Decorative Books -->

            <div
                class="home-search-cta-books"
                aria-hidden="true"
            >

                <i class="fa-solid fa-book"></i>
                <i class="fa-solid fa-book-open"></i>
                <i class="fa-solid fa-graduation-cap"></i>

            </div>

        </div>

    </div>

</section>

    </div>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>