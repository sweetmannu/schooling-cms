<?php

/*
|--------------------------------------------------------------------------
| Navbar Settings
|--------------------------------------------------------------------------
*/

$siteName = setting(
    'site_name',
    APP_NAME
);

$siteTagline = setting(
    'site_tagline',
    'Learn • Practice • Grow'
);

$siteLogo = setting(
    'site_logo',
    ''
);

$logoUrl = '';

if (!empty($siteLogo)) {

    $safeLogo = basename(
        (string) $siteLogo
    );

    $logoUrl =
        rtrim(APP_URL, '/')
        . '/uploads/logo/'
        . rawurlencode($safeLogo);
}


/*
|--------------------------------------------------------------------------
| Current Request Path
|--------------------------------------------------------------------------
*/

$requestPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?: '/';

$appPath = parse_url(
    APP_URL,
    PHP_URL_PATH
) ?: '';

$appPath = rtrim(
    $appPath,
    '/'
);

if (
    $appPath !== '' &&
    (
        $requestPath === $appPath ||
        str_starts_with(
            $requestPath,
            $appPath . '/'
        )
    )
) {

    $requestPath = substr(
        $requestPath,
        strlen($appPath)
    );
}

$requestPath =
    '/' . ltrim(
        $requestPath,
        '/'
    );


/*
|--------------------------------------------------------------------------
| Active Navigation Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('frontendNavActive')) {

    function frontendNavActive(
        string $requestPath,
        array $paths
    ): bool {

        foreach ($paths as $path) {

            if ($requestPath === $path) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('frontendNavStartsWith')) {

    function frontendNavStartsWith(
        string $requestPath,
        array $prefixes
    ): bool {

        foreach ($prefixes as $prefix) {

            if (
                str_starts_with(
                    $requestPath,
                    $prefix
                )
            ) {
                return true;
            }
        }

        return false;
    }
}


/*
|--------------------------------------------------------------------------
| Active Navigation States
|--------------------------------------------------------------------------
*/

$isHomeActive = frontendNavActive(
    $requestPath,
    [
        '/',
        '/index.php'
    ]
);

$isCategoriesActive =
    frontendNavStartsWith(
        $requestPath,
        [
            '/category/',
            '/subject/',
            '/chapter/',
            '/note/'
        ]
    );

$isAboutActive = frontendNavActive(
    $requestPath,
    [
        '/about.php',
        '/about'
    ]
);

$isContactActive = frontendNavActive(
    $requestPath,
    [
        '/contact.php',
        '/contact'
    ]
);


/*
|--------------------------------------------------------------------------
| Search Value
|--------------------------------------------------------------------------
*/

$searchValue = '';

if (
    isset($_GET['q']) &&
    is_string($_GET['q'])
) {

    $searchValue = trim(
        $_GET['q']
    );

    if (
        mb_strlen(
            $searchValue,
            'UTF-8'
        ) > 100
    ) {

        $searchValue = mb_substr(
            $searchValue,
            0,
            100,
            'UTF-8'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Common URLs
|--------------------------------------------------------------------------
*/

$baseUrl = rtrim(
    APP_URL,
    '/'
);

$homeUrl =
    $baseUrl . '/';

$categoriesUrl =
    $baseUrl . '/';

$aboutUrl =
    $baseUrl . '/about.php';

$contactUrl =
    $baseUrl . '/contact.php';

$searchUrl =
    $baseUrl . '/search.php';

?>

<nav
    class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top main-navbar"
    aria-label="Main navigation"
>

    <div class="container">

        <!-- ==================================================
             Brand
        =================================================== -->

        <a
            class="navbar-brand d-flex align-items-center"
            href="<?= htmlspecialchars(
                $homeUrl,
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
            aria-label="<?= htmlspecialchars(
                $siteName . ' Home',
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
        >

            <?php if ($logoUrl !== '') { ?>

                <img
                    src="<?= htmlspecialchars(
                        $logoUrl,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    alt="<?= htmlspecialchars(
                        $siteName,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    class="navbar-site-logo me-2"
                    style="
                        max-height:52px;
                        width:auto;
                        object-fit:contain;
                    "
                >

            <?php } else { ?>

                <span
                    class="d-inline-flex align-items-center justify-content-center me-2"
                    aria-hidden="true"
                    style="
                        width:46px;
                        height:46px;
                        border-radius:12px;
                        background:rgba(255,255,255,.12);
                    "
                >

                    <i
                        class="fa-solid fa-graduation-cap fs-3"
                    ></i>

                </span>

            <?php } ?>


            <span class="navbar-brand-text">

                <span
                    class="d-block fw-bold lh-sm"
                    style="
                        font-size:1.05rem;
                        letter-spacing:.02em;
                    "
                >

                    <?= htmlspecialchars(
                        $siteName,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </span>

                <?php if (!empty($siteTagline)) { ?>

                    <small
                        class="d-none d-sm-block text-white-50"
                        style="
                            font-size:11px;
                            line-height:1.3;
                            letter-spacing:.02em;
                        "
                    >

                        <?= htmlspecialchars(
                            $siteTagline,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </small>

                <?php } ?>

            </span>

        </a>


        <!-- ==================================================
             Mobile Toggle
        =================================================== -->

        <button
            class="navbar-toggler border-0 shadow-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <!-- ==================================================
             Navigation
        =================================================== -->

        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul
                class="navbar-nav ms-auto align-items-lg-center gap-lg-1"
            >

                <!-- Home -->

                <li class="nav-item">

                    <a
                        class="nav-link px-lg-3<?= $isHomeActive
                            ? ' active fw-semibold'
                            : ''; ?>"
                        href="<?= htmlspecialchars(
                            $homeUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= $isHomeActive
                            ? 'aria-current="page"'
                            : ''; ?>
                    >

                        Home

                    </a>

                </li>


                <!-- Categories -->

                <li class="nav-item">

                    <a
                        class="nav-link px-lg-3<?= $isCategoriesActive
                            ? ' active fw-semibold'
                            : ''; ?>"
                        href="<?= htmlspecialchars(
                            $categoriesUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= $isCategoriesActive
                            ? 'aria-current="page"'
                            : ''; ?>
                    >

                        Categories

                    </a>

                </li>


                <!-- About -->

                <li class="nav-item">

                    <a
                        class="nav-link px-lg-3<?= $isAboutActive
                            ? ' active fw-semibold'
                            : ''; ?>"
                        href="<?= htmlspecialchars(
                            $aboutUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= $isAboutActive
                            ? 'aria-current="page"'
                            : ''; ?>
                    >

                        About

                    </a>

                </li>


                <!-- Contact -->

                <li class="nav-item">

                    <a
                        class="nav-link px-lg-3<?= $isContactActive
                            ? ' active fw-semibold'
                            : ''; ?>"
                        href="<?= htmlspecialchars(
                            $contactUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        <?= $isContactActive
                            ? 'aria-current="page"'
                            : ''; ?>
                    >

                        Contact

                    </a>

                </li>


                <!-- Search -->

                <li
                    class="nav-item ms-lg-3 mt-3 mt-lg-0"
                >

                    <form
                        action="<?= htmlspecialchars(
                            $searchUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        method="GET"
                        role="search"
                        class="navbar-search-form"
                    >

                        <div
                            class="input-group"
                            style="
                                width:min(100%,280px);
                            "
                        >

                            <label
                                for="navbar-search"
                                class="visually-hidden"
                            >
                                Search notes
                            </label>

                            <input
                                id="navbar-search"
                                class="form-control border-0"
                                type="search"
                                name="q"
                                placeholder="Search notes..."
                                value="<?= htmlspecialchars(
                                    $searchValue,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                maxlength="100"
                                autocomplete="off"
                                required
                            >

                            <button
                                class="btn btn-light border-0"
                                type="submit"
                                aria-label="Search"
                            >

                                <i
                                    class="fa-solid fa-magnifying-glass"
                                    aria-hidden="true"
                                ></i>

                            </button>

                        </div>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</nav>