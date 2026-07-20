<?php

/*
|--------------------------------------------------------------------------
| Footer Settings
|--------------------------------------------------------------------------
*/

$footerBaseUrl = rtrim(APP_URL, '/');

$footerSiteName = trim(
    (string) setting(
        'site_name',
        APP_NAME
    )
);

$footerTagline = trim(
    (string) setting(
        'site_tagline',
        'Learn • Practice • Grow'
    )
);

$footerDescription = trim(
    (string) setting(
        'footer_description',
        'Schooling Education is a modern learning platform that provides high-quality study notes, chapters, subjects, PDFs and learning resources for students.'
    )
);

$facebookUrl = trim(
    (string) setting('facebook_url', '')
);

$instagramUrl = trim(
    (string) setting('instagram_url', '')
);

$youtubeUrl = trim(
    (string) setting('youtube_url', '')
);

$linkedinUrl = trim(
    (string) setting('linkedin_url', '')
);

$githubUrl = trim(
    (string) setting('github_url', '')
);

$contactEmail = trim(
    (string) setting('contact_email', '')
);

$contactPhone = trim(
    (string) setting('contact_phone', '')
);

$contactAddress = trim(
    (string) setting('address', '')
);

$copyrightText = trim(
    (string) setting(
        'copyright_text',
        $footerSiteName . ' - All Rights Reserved.'
    )
);

/*
|--------------------------------------------------------------------------
| Validate Social URLs
|--------------------------------------------------------------------------
*/

$facebookUrl = filter_var(
    $facebookUrl,
    FILTER_VALIDATE_URL
) ?: '';

$instagramUrl = filter_var(
    $instagramUrl,
    FILTER_VALIDATE_URL
) ?: '';

$youtubeUrl = filter_var(
    $youtubeUrl,
    FILTER_VALIDATE_URL
) ?: '';

$linkedinUrl = filter_var(
    $linkedinUrl,
    FILTER_VALIDATE_URL
) ?: '';

$githubUrl = filter_var(
    $githubUrl,
    FILTER_VALIDATE_URL
) ?: '';

/*
|--------------------------------------------------------------------------
| Validate Contact Information
|--------------------------------------------------------------------------
*/

$validContactEmail = filter_var(
    $contactEmail,
    FILTER_VALIDATE_EMAIL
) ?: '';

$phoneHref = '';

if ($contactPhone !== '') {

    $phoneHref = preg_replace(
        '/[^0-9+]/',
        '',
        $contactPhone
    );

    if (!is_string($phoneHref)) {
        $phoneHref = '';
    }

}

?>

<footer class="site-footer">

    <div class="container">

        <div class="site-footer-main">

            <div class="row g-4 g-lg-5">

                <!-- Brand -->

                <div class="col-lg-4 col-md-6">

                    <div class="footer-brand">

                        <a
                            href="<?= htmlspecialchars(
                                $footerBaseUrl . '/',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="footer-brand-title"
                        >

                            <span class="footer-brand-icon">

                                <i
                                    class="fa-solid fa-graduation-cap"
                                    aria-hidden="true"
                                ></i>

                            </span>

                            <span>

                                <?= htmlspecialchars(
                                    $footerSiteName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>

                        </a>


                        <?php if ($footerTagline !== '') { ?>

                            <p class="footer-tagline">

                                <?= htmlspecialchars(
                                    $footerTagline,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </p>

                        <?php } ?>


                        <?php if ($footerDescription !== '') { ?>

                            <p class="footer-description">

                                <?= nl2br(
                                    htmlspecialchars(
                                        $footerDescription,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                ); ?>

                            </p>

                        <?php } ?>

                    </div>

                </div>


                <!-- Quick Links -->

                <div class="col-lg-2 col-md-6">

                    <div class="footer-column">

                        <h2 class="footer-heading">
                            Quick Links
                        </h2>

                        <ul class="footer-links">

                            <li>

                                <a
                                    href="<?= htmlspecialchars(
                                        $footerBaseUrl . '/',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >
                                    Home
                                </a>

                            </li>

                            <li>

                                <a
                                    href="<?= htmlspecialchars(
                                        $footerBaseUrl . '/search.php',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >
                                    Search
                                </a>

                            </li>

                            <li>

                                <a
                                    href="<?= htmlspecialchars(
                                        $footerBaseUrl . '/about.php',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >
                                    About
                                </a>

                            </li>

                            <li>

                                <a
                                    href="<?= htmlspecialchars(
                                        $footerBaseUrl . '/contact.php',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >
                                    Contact
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>


                <!-- Study Resources -->

                <div class="col-lg-3 col-md-6">

                    <div class="footer-column">

                        <h2 class="footer-heading">
                            Study Resources
                        </h2>

                        <ul class="footer-links">

                            <li>

                                <a href="<?= htmlspecialchars(
                                    $footerBaseUrl . '/#browse-classes',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>">
                                    Browse Classes
                                </a>

                            </li>

                            <li>

                                <a href="<?= htmlspecialchars(
                                    $footerBaseUrl . '/#popular-subjects',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>">
                                    Popular Subjects
                                </a>

                            </li>

                            <li>

                                <a href="<?= htmlspecialchars(
                                    $footerBaseUrl . '/#latest-notes',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>">
                                    Latest Notes
                                </a>

                            </li>

                            <li>

                                <a href="<?= htmlspecialchars(
                                    $footerBaseUrl . '/sitemap.php',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>">
                                    Sitemap
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>


                <!-- Connect -->

                <div class="col-lg-3 col-md-6">

                    <div class="footer-column">

                        <h2 class="footer-heading">
                            Connect With Us
                        </h2>


                        <?php if (
                            $facebookUrl !== '' ||
                            $instagramUrl !== '' ||
                            $youtubeUrl !== '' ||
                            $linkedinUrl !== '' ||
                            $githubUrl !== ''
                        ) { ?>

                            <div class="footer-socials">

                                <?php if ($facebookUrl !== '') { ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $facebookUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Facebook"
                                    >
                                        <i
                                            class="fa-brands fa-facebook-f"
                                            aria-hidden="true"
                                        ></i>
                                    </a>

                                <?php } ?>


                                <?php if ($instagramUrl !== '') { ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $instagramUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Instagram"
                                    >
                                        <i
                                            class="fa-brands fa-instagram"
                                            aria-hidden="true"
                                        ></i>
                                    </a>

                                <?php } ?>


                                <?php if ($youtubeUrl !== '') { ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $youtubeUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="YouTube"
                                    >
                                        <i
                                            class="fa-brands fa-youtube"
                                            aria-hidden="true"
                                        ></i>
                                    </a>

                                <?php } ?>


                                <?php if ($linkedinUrl !== '') { ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $linkedinUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="LinkedIn"
                                    >
                                        <i
                                            class="fa-brands fa-linkedin-in"
                                            aria-hidden="true"
                                        ></i>
                                    </a>

                                <?php } ?>


                                <?php if ($githubUrl !== '') { ?>

                                    <a
                                        href="<?= htmlspecialchars(
                                            $githubUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="GitHub"
                                    >
                                        <i
                                            class="fa-brands fa-github"
                                            aria-hidden="true"
                                        ></i>
                                    </a>

                                <?php } ?>

                            </div>

                        <?php } ?>


                        <div class="footer-contact-list">

                            <?php if ($validContactEmail !== '') { ?>

                                <div class="footer-contact-item">

                                    <span class="footer-contact-icon">

                                        <i
                                            class="fa-regular fa-envelope"
                                            aria-hidden="true"
                                        ></i>

                                    </span>

                                    <a
                                        href="mailto:<?= htmlspecialchars(
                                            $validContactEmail,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $validContactEmail,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </a>

                                </div>

                            <?php } ?>


                            <?php if (
                                $contactPhone !== '' &&
                                $phoneHref !== ''
                            ) { ?>

                                <div class="footer-contact-item">

                                    <span class="footer-contact-icon">

                                        <i
                                            class="fa-solid fa-phone"
                                            aria-hidden="true"
                                        ></i>

                                    </span>

                                    <a
                                        href="tel:<?= htmlspecialchars(
                                            $phoneHref,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $contactPhone,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </a>

                                </div>

                            <?php } ?>


                            <?php if ($contactAddress !== '') { ?>

                                <div class="footer-contact-item">

                                    <span class="footer-contact-icon">

                                        <i
                                            class="fa-solid fa-location-dot"
                                            aria-hidden="true"
                                        ></i>

                                    </span>

                                    <address>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $contactAddress,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        ); ?>

                                    </address>

                                </div>

                            <?php } ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Bottom -->

        <div class="site-footer-bottom">

            <p>

                &copy;
                <?= htmlspecialchars(
                    date('Y'),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

                <?= htmlspecialchars(
                    preg_replace(
                        '/^(?:©|&copy;)?\s*\d{4}\s*/u',
                        '',
                        $copyrightText
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </p>


            <p class="footer-made">

                Made with

                <span
                    role="img"
                    aria-label="love"
                >
                    ❤️
                </span>

                in India

                <span class="footer-divider">
                    |
                </span>

                Version 1.0.0

            </p>

        </div>

    </div>

</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    defer>
</script>

</body>
</html>