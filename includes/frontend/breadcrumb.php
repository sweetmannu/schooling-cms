<?php

/*
|--------------------------------------------------------------------------
| Breadcrumb Builder
|--------------------------------------------------------------------------
|
| Supported page variables:
|
| $category
| $subject
| $chapter
| $note
|
| This file safely creates the complete hierarchy:
|
| Home > Category > Subject > Chapter > Note
|
|--------------------------------------------------------------------------
*/

$breadcrumbs = [];

$baseUrl = rtrim(APP_URL, '/');

/*
|--------------------------------------------------------------------------
| Helper: Add Breadcrumb Without Duplicates
|--------------------------------------------------------------------------
*/

if (!function_exists('addFrontendBreadcrumb')) {

    function addFrontendBreadcrumb(
        array &$breadcrumbs,
        string $title,
        string $url
    ): void {

        $title = trim($title);
        $url = trim($url);

        if ($title === '' || $url === '') {
            return;
        }

        /*
        | Avoid duplicate breadcrumb URLs
        */

        foreach ($breadcrumbs as $breadcrumb) {

            if (
                isset($breadcrumb['url']) &&
                $breadcrumb['url'] === $url
            ) {
                return;
            }

        }

        $breadcrumbs[] = [
            'title' => $title,
            'url'   => $url
        ];

    }

}

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

addFrontendBreadcrumb(
    $breadcrumbs,
    'Home',
    $baseUrl . '/'
);

/*
|--------------------------------------------------------------------------
| Resolve Breadcrumb Data
|--------------------------------------------------------------------------
*/

$categoryName = '';
$categorySlug = '';

$subjectName = '';
$subjectSlug = '';

$chapterName = '';
$chapterSlug = '';

$noteTitle = '';
$noteSlug = '';

/*
|--------------------------------------------------------------------------
| Category Page Data
|--------------------------------------------------------------------------
*/

if (isset($category) && is_array($category)) {

    $categoryName = isset($category['category_name'])
        ? trim((string)$category['category_name'])
        : '';

    $categorySlug = isset($category['slug'])
        ? trim((string)$category['slug'])
        : '';

}

/*
|--------------------------------------------------------------------------
| Subject Page Data
|--------------------------------------------------------------------------
*/

if (isset($subject) && is_array($subject)) {

    $subjectName = isset($subject['subject_name'])
        ? trim((string)$subject['subject_name'])
        : '';

    $subjectSlug = isset($subject['slug'])
        ? trim((string)$subject['slug'])
        : '';

    /*
    | Parent category details may be available
    | inside the subject query.
    */

    if (
        $categoryName === '' &&
        isset($subject['category_name'])
    ) {
        $categoryName = trim(
            (string)$subject['category_name']
        );
    }

    if (
        $categorySlug === '' &&
        isset($subject['category_slug'])
    ) {
        $categorySlug = trim(
            (string)$subject['category_slug']
        );
    }

}

/*
|--------------------------------------------------------------------------
| Chapter Page Data
|--------------------------------------------------------------------------
*/

if (isset($chapter) && is_array($chapter)) {

    $chapterName = isset($chapter['chapter_name'])
        ? trim((string)$chapter['chapter_name'])
        : '';

    $chapterSlug = isset($chapter['slug'])
        ? trim((string)$chapter['slug'])
        : '';

    /*
    | Parent subject details
    */

    if (
        $subjectName === '' &&
        isset($chapter['subject_name'])
    ) {
        $subjectName = trim(
            (string)$chapter['subject_name']
        );
    }

    if (
        $subjectSlug === '' &&
        isset($chapter['subject_slug'])
    ) {
        $subjectSlug = trim(
            (string)$chapter['subject_slug']
        );
    }

    /*
    | Parent category details
    */

    if (
        $categoryName === '' &&
        isset($chapter['category_name'])
    ) {
        $categoryName = trim(
            (string)$chapter['category_name']
        );
    }

    if (
        $categorySlug === '' &&
        isset($chapter['category_slug'])
    ) {
        $categorySlug = trim(
            (string)$chapter['category_slug']
        );
    }

}

/*
|--------------------------------------------------------------------------
| Note Page Data
|--------------------------------------------------------------------------
*/

if (isset($note) && is_array($note)) {

    $noteTitle = isset($note['title'])
        ? trim((string)$note['title'])
        : '';

    $noteSlug = isset($note['slug'])
        ? trim((string)$note['slug'])
        : '';

    /*
    | Chapter details from note query
    */

    if (
        $chapterName === '' &&
        isset($note['chapter_name'])
    ) {
        $chapterName = trim(
            (string)$note['chapter_name']
        );
    }

    if (
        $chapterSlug === '' &&
        isset($note['chapter_slug'])
    ) {
        $chapterSlug = trim(
            (string)$note['chapter_slug']
        );
    }

    /*
    | Subject details from note query
    */

    if (
        $subjectName === '' &&
        isset($note['subject_name'])
    ) {
        $subjectName = trim(
            (string)$note['subject_name']
        );
    }

    if (
        $subjectSlug === '' &&
        isset($note['subject_slug'])
    ) {
        $subjectSlug = trim(
            (string)$note['subject_slug']
        );
    }

    /*
    | Category details from note query
    */

    if (
        $categoryName === '' &&
        isset($note['category_name'])
    ) {
        $categoryName = trim(
            (string)$note['category_name']
        );
    }

    if (
        $categorySlug === '' &&
        isset($note['category_slug'])
    ) {
        $categorySlug = trim(
            (string)$note['category_slug']
        );
    }

}

/*
|--------------------------------------------------------------------------
| Add Category
|--------------------------------------------------------------------------
*/

if ($categoryName !== '' && $categorySlug !== '') {

    addFrontendBreadcrumb(
        $breadcrumbs,
        $categoryName,
        $baseUrl
            . '/category/'
            . rawurlencode($categorySlug)
    );

}

/*
|--------------------------------------------------------------------------
| Add Subject
|--------------------------------------------------------------------------
*/

if ($subjectName !== '' && $subjectSlug !== '') {

    addFrontendBreadcrumb(
        $breadcrumbs,
        $subjectName,
        $baseUrl
            . '/subject/'
            . rawurlencode($subjectSlug)
    );

}

/*
|--------------------------------------------------------------------------
| Add Chapter
|--------------------------------------------------------------------------
*/

if ($chapterName !== '' && $chapterSlug !== '') {

    addFrontendBreadcrumb(
        $breadcrumbs,
        $chapterName,
        $baseUrl
            . '/chapter/'
            . rawurlencode($chapterSlug)
    );

}

/*
|--------------------------------------------------------------------------
| Add Note
|--------------------------------------------------------------------------
*/

if ($noteTitle !== '' && $noteSlug !== '') {

    addFrontendBreadcrumb(
        $breadcrumbs,
        $noteTitle,
        $baseUrl
            . '/note/'
            . rawurlencode($noteSlug)
    );

}

/*
|--------------------------------------------------------------------------
| Breadcrumb Count
|--------------------------------------------------------------------------
*/

$totalBreadcrumbs = count($breadcrumbs);

?>

<?php if ($totalBreadcrumbs > 1) { ?>

    <nav
        class="mb-4"
        aria-label="Breadcrumb">

        <ol class="breadcrumb mb-0">

            <?php foreach ($breadcrumbs as $index => $item) { ?>

                <?php
                $isLast = (
                    $index === $totalBreadcrumbs - 1
                );
                ?>

                <?php if ($isLast) { ?>

                    <li
                        class="breadcrumb-item active"
                        aria-current="page">

                        <?= htmlspecialchars(
                            $item['title'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </li>

                <?php } else { ?>

                    <li class="breadcrumb-item">

                        <a
                            href="<?= htmlspecialchars(
                                $item['url'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>">

                            <?= htmlspecialchars(
                                $item['title'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </a>

                    </li>

                <?php } ?>

            <?php } ?>

        </ol>

    </nav>

<?php } ?>

<?php

/*
|--------------------------------------------------------------------------
| Breadcrumb JSON-LD Schema
|--------------------------------------------------------------------------
*/

if ($totalBreadcrumbs > 1) {

    $schemaItems = [];

    foreach ($breadcrumbs as $position => $item) {

        $schemaItems[] = [
            '@type'    => 'ListItem',
            'position' => $position + 1,
            'name'     => $item['title'],
            'item'     => $item['url']
        ];

    }

    $breadcrumbSchema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $schemaItems
    ];

    $encodedBreadcrumbSchema = json_encode(
        $breadcrumbSchema,
        JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    );

    if ($encodedBreadcrumbSchema !== false) {

        ?>

        <script type="application/ld+json">
<?= $encodedBreadcrumbSchema; ?>
        </script>

        <?php

    }

}

?>