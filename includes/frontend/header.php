<?php

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../../config/config.php';
}

require_once __DIR__ . '/../settings.php';

/*
|--------------------------------------------------------------------------
| Base Website URL
|--------------------------------------------------------------------------
*/

$baseUrl = rtrim((string) APP_URL, '/');

/*
|--------------------------------------------------------------------------
| Safe Settings
|--------------------------------------------------------------------------
*/

$siteName = trim(
    (string) setting(
        'site_name',
        APP_NAME
    )
);

if ($siteName === '') {
    $siteName = APP_NAME;
}

$defaultMetaTitle = trim(
    (string) setting(
        'default_meta_title',
        $siteName
    )
);

if ($defaultMetaTitle === '') {
    $defaultMetaTitle = $siteName;
}

$defaultMetaDescription = trim(
    (string) setting(
        'default_meta_description',
        'Schooling Education CMS'
    )
);

if ($defaultMetaDescription === '') {
    $defaultMetaDescription = 'Schooling Education CMS';
}

/*
|--------------------------------------------------------------------------
| Default SEO Values
|--------------------------------------------------------------------------
*/

$pageTitle = isset($pageTitle) && is_scalar($pageTitle)
    ? trim((string) $pageTitle)
    : $defaultMetaTitle;

if ($pageTitle === '') {
    $pageTitle = $defaultMetaTitle;
}

$pageDescription = isset($pageDescription) && is_scalar($pageDescription)
    ? trim(strip_tags((string) $pageDescription))
    : $defaultMetaDescription;

if ($pageDescription === '') {
    $pageDescription = $defaultMetaDescription;
}

$robotsMeta = isset($robotsMeta) && is_scalar($robotsMeta)
    ? trim((string) $robotsMeta)
    : 'index,follow';

if ($robotsMeta === '') {
    $robotsMeta = 'index,follow';
}

/*
|--------------------------------------------------------------------------
| Dynamic Website Logo
|--------------------------------------------------------------------------
*/

$siteLogo = trim(
    (string) setting(
        'site_logo',
        ''
    )
);

if ($siteLogo !== '') {

    $schemaLogo = $baseUrl
        . '/uploads/logo/'
        . rawurlencode(basename($siteLogo));

} else {

    $schemaLogo = $baseUrl
        . '/uploads/logo/logo_1783960166.jpg';

}

/*
|--------------------------------------------------------------------------
| Dynamic Favicon
|--------------------------------------------------------------------------
*/

$siteFavicon = trim(
    (string) setting(
        'site_favicon',
        ''
    )
);

$faviconUrl = '';

if ($siteFavicon !== '') {

    $faviconUrl = $baseUrl
        . '/uploads/logo/favicon/'
        . rawurlencode(basename($siteFavicon));

}

/*
|--------------------------------------------------------------------------
| Default Open Graph Values
|--------------------------------------------------------------------------
*/

$ogImage = $schemaLogo;
$ogType = 'website';

/*
|--------------------------------------------------------------------------
| Resolve Valid Page Records
|--------------------------------------------------------------------------
|
| isset() alone is not sufficient because a failed PDO fetch returns false.
|
|--------------------------------------------------------------------------
*/

$validNote = (
    isset($note) &&
    is_array($note) &&
    !empty($note)
);

$validChapter = (
    isset($chapter) &&
    is_array($chapter) &&
    !empty($chapter)
);

$validSubject = (
    isset($subject) &&
    is_array($subject) &&
    !empty($subject)
);

$validCategory = (
    isset($category) &&
    is_array($category) &&
    !empty($category)
);

/*
|--------------------------------------------------------------------------
| Page-Specific SEO
|--------------------------------------------------------------------------
*/

if ($validNote) {

    $ogType = 'article';

    $noteTitle = isset($note['title'])
        ? trim((string) $note['title'])
        : '';

    $noteMetaTitle = isset($note['meta_title'])
        ? trim((string) $note['meta_title'])
        : '';

    $noteMetaDescription = isset($note['meta_description'])
        ? trim(strip_tags((string) $note['meta_description']))
        : '';

    $noteShortDescription = isset($note['short_description'])
        ? trim(strip_tags((string) $note['short_description']))
        : '';

    if ($noteMetaTitle !== '') {

        $pageTitle = $noteMetaTitle;

    } elseif ($noteTitle !== '') {

        $pageTitle = $noteTitle
            . ' | '
            . $siteName;

    }

    if ($noteMetaDescription !== '') {

        $pageDescription = $noteMetaDescription;

    } elseif ($noteShortDescription !== '') {

        $pageDescription = $noteShortDescription;

    } else {

        $pageDescription = 'Study notes and learning resources.';

    }

    $noteThumbnail = isset($note['thumbnail'])
        ? trim((string) $note['thumbnail'])
        : '';

    if ($noteThumbnail !== '') {

        $ogImage = $baseUrl
            . '/uploads/thumbnails/'
            . rawurlencode(basename($noteThumbnail));

    }

} elseif ($validChapter) {

    $chapterName = isset($chapter['chapter_name'])
        ? trim((string) $chapter['chapter_name'])
        : '';

    if ($chapterName !== '') {

        $pageTitle = $chapterName
            . ' | '
            . $siteName;

        $pageDescription = 'Study notes for '
            . $chapterName
            . '.';

    }

} elseif ($validSubject) {

    $subjectName = isset($subject['subject_name'])
        ? trim((string) $subject['subject_name'])
        : '';

    if ($subjectName !== '') {

        $pageTitle = $subjectName
            . ' | '
            . $siteName;

        $pageDescription = 'Browse chapters and study resources for '
            . $subjectName
            . '.';

    }

} elseif ($validCategory) {

    $categoryName = isset($category['category_name'])
        ? trim((string) $category['category_name'])
        : '';

    if ($categoryName !== '') {

        $pageTitle = $categoryName
            . ' | '
            . $siteName;

        $pageDescription = 'Browse '
            . $categoryName
            . ' subjects, chapters and study notes.';

    }

}

/*
|--------------------------------------------------------------------------
| Final SEO Text Cleanup
|--------------------------------------------------------------------------
*/

$pageTitle = trim(strip_tags($pageTitle));

if ($pageTitle === '') {
    $pageTitle = $defaultMetaTitle;
}

$pageDescription = trim(
    preg_replace(
        '/\s+/u',
        ' ',
        strip_tags($pageDescription)
    ) ?? ''
);

if ($pageDescription === '') {
    $pageDescription = $defaultMetaDescription;
}

/*
|--------------------------------------------------------------------------
| Clean Canonical URL
|--------------------------------------------------------------------------
*/

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

$requestPath = parse_url(
    $requestUri,
    PHP_URL_PATH
);

if (!is_string($requestPath) || $requestPath === '') {
    $requestPath = '/';
}

$appPath = parse_url(
    $baseUrl,
    PHP_URL_PATH
);

if (!is_string($appPath)) {
    $appPath = '';
}

$appPath = rtrim($appPath, '/');

if (
    $appPath !== '' &&
    (
        $requestPath === $appPath ||
        str_starts_with($requestPath, $appPath . '/')
    )
) {

    $requestPath = substr(
        $requestPath,
        strlen($appPath)
    );

}

$requestPath = '/' . ltrim($requestPath, '/');

if ($requestPath !== '/') {
    $requestPath = rtrim($requestPath, '/');
}

$currentUrl = $baseUrl . $requestPath;

/*
|--------------------------------------------------------------------------
| Article Structured Data
|--------------------------------------------------------------------------
*/

$articleSchema = null;

if ($validNote) {

    $articleHeadline = isset($note['title'])
        ? trim((string) $note['title'])
        : $pageTitle;

    $datePublished = null;
    $dateModified = null;

    if (!empty($note['created_at'])) {

        $createdTimestamp = strtotime(
            (string) $note['created_at']
        );

        if ($createdTimestamp !== false) {
            $datePublished = date(
                DATE_ATOM,
                $createdTimestamp
            );
        }

    }

    if (!empty($note['updated_at'])) {

        $updatedTimestamp = strtotime(
            (string) $note['updated_at']
        );

        if ($updatedTimestamp !== false) {
            $dateModified = date(
                DATE_ATOM,
                $updatedTimestamp
            );
        }

    }

    if ($dateModified === null) {
        $dateModified = $datePublished;
    }

    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $articleHeadline,
        'description' => $pageDescription,
        'image' => [
            $ogImage
        ],
        'author' => [
            '@type' => 'Organization',
            'name' => $siteName
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $schemaLogo
            ]
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $currentUrl
        ]
    ];

    if ($datePublished !== null) {
        $articleSchema['datePublished'] = $datePublished;
    }

    if ($dateModified !== null) {
        $articleSchema['dateModified'] = $dateModified;
    }

}

/*
|--------------------------------------------------------------------------
| Encode Article Schema
|--------------------------------------------------------------------------
*/

$encodedArticleSchema = '';

if ($articleSchema !== null) {

    $schemaJson = json_encode(
        $articleSchema,
        JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    );

    if (is_string($schemaJson)) {
        $encodedArticleSchema = $schemaJson;
    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <?php if ($faviconUrl !== '') { ?>

        <link
            rel="icon"
            href="<?= htmlspecialchars(
                $faviconUrl,
                ENT_QUOTES,
                'UTF-8'
            ); ?>">

    <?php } ?>

    <title><?= htmlspecialchars(
        $pageTitle,
        ENT_QUOTES,
        'UTF-8'
    ); ?></title>

    <meta
        name="description"
        content="<?= htmlspecialchars(
            $pageDescription,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        name="robots"
        content="<?= htmlspecialchars(
            $robotsMeta,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <link
        rel="canonical"
        href="<?= htmlspecialchars(
            $currentUrl,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <!-- Open Graph -->

    <meta
        property="og:type"
        content="<?= htmlspecialchars(
            $ogType,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        property="og:site_name"
        content="<?= htmlspecialchars(
            $siteName,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        property="og:title"
        content="<?= htmlspecialchars(
            $pageTitle,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        property="og:description"
        content="<?= htmlspecialchars(
            $pageDescription,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        property="og:url"
        content="<?= htmlspecialchars(
            $currentUrl,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        property="og:image"
        content="<?= htmlspecialchars(
            $ogImage,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <!-- Twitter Card -->

    <meta
        name="twitter:card"
        content="summary_large_image">

    <meta
        name="twitter:title"
        content="<?= htmlspecialchars(
            $pageTitle,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        name="twitter:description"
        content="<?= htmlspecialchars(
            $pageDescription,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        name="twitter:image"
        content="<?= htmlspecialchars(
            $ogImage,
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <meta
        name="theme-color"
        content="#0d6efd">

    <?php if ($encodedArticleSchema !== '') { ?>

        <script type="application/ld+json">
<?= $encodedArticleSchema; ?>
        </script>

    <?php } ?>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            $baseUrl . '/assets/css/style.css',
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

</head>

<body class="bg-light">