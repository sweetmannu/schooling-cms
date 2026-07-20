<?php

require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/xml; charset=UTF-8');

function sitemapUrl(string $path): string
{
    $url = rtrim(APP_URL, '/') . '/' . ltrim($path, '/');

    return htmlspecialchars(
        $url,
        ENT_XML1 | ENT_QUOTES,
        'UTF-8'
    );
}

function sitemapDate(?string $date): string
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return '';
    }

    return date('c', $timestamp);
}

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <!-- Home -->

    <url>

        <loc><?= sitemapUrl('/'); ?></loc>

        <changefreq>daily</changefreq>

        <priority>1.0</priority>

    </url>

    <?php

    /* Categories */

    $stmt = $pdo->query("
        SELECT slug, updated_at
        FROM categories
        WHERE status = 'Active'
        AND parent_id IS NULL
        ORDER BY id DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $categoryPath = '/category/' . rawurlencode($row['slug']);
        $lastmod = sitemapDate($row['updated_at'] ?? null);

    ?>

        <url>

            <loc><?= sitemapUrl($categoryPath); ?></loc>

            <?php if ($lastmod !== '') { ?>
                <lastmod><?= $lastmod; ?></lastmod>
            <?php } ?>

            <changefreq>weekly</changefreq>

            <priority>0.9</priority>

        </url>

    <?php } ?>

    <?php

    /* Subjects */

    $stmt = $pdo->query("
        SELECT slug, updated_at
        FROM subjects
        WHERE status = 'Active'
        ORDER BY id DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $subjectPath = '/subject/' . rawurlencode($row['slug']);
        $lastmod = sitemapDate($row['updated_at'] ?? null);

    ?>

        <url>

            <loc><?= sitemapUrl($subjectPath); ?></loc>

            <?php if ($lastmod !== '') { ?>
                <lastmod><?= $lastmod; ?></lastmod>
            <?php } ?>

            <changefreq>weekly</changefreq>

            <priority>0.8</priority>

        </url>

    <?php } ?>

    <?php

    /* Chapters */

    $stmt = $pdo->query("
        SELECT slug, updated_at
        FROM chapters
        WHERE status = 'Active'
        ORDER BY id DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $chapterPath = '/chapter/' . rawurlencode($row['slug']);
        $lastmod = sitemapDate($row['updated_at'] ?? null);

    ?>

        <url>

            <loc><?= sitemapUrl($chapterPath); ?></loc>

            <?php if ($lastmod !== '') { ?>
                <lastmod><?= $lastmod; ?></lastmod>
            <?php } ?>

            <changefreq>weekly</changefreq>

            <priority>0.8</priority>

        </url>

    <?php } ?>

    <?php

    /* Notes */

    $stmt = $pdo->query("
        SELECT slug, updated_at
        FROM notes
        WHERE status = 'Active'
        ORDER BY id DESC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $notePath = '/note/' . rawurlencode($row['slug']);
        $lastmod = sitemapDate($row['updated_at'] ?? null);

    ?>

        <url>

            <loc><?= sitemapUrl($notePath); ?></loc>

            <?php if ($lastmod !== '') { ?>
                <lastmod><?= $lastmod; ?></lastmod>
            <?php } ?>

            <changefreq>monthly</changefreq>

            <priority>0.7</priority>

        </url>

    <?php } ?>

</urlset>