<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}


verify_csrf_token($_POST['csrf_token'] ?? '');

/* Get Form Data */

$site_name = trim($_POST['site_name'] ?? '');
$site_tagline = trim($_POST['site_tagline'] ?? '');
$contact_email = trim($_POST['contact_email'] ?? '');
$contact_phone = trim($_POST['contact_phone'] ?? '');
$whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
$address = trim($_POST['address'] ?? '');

$facebook_url = trim($_POST['facebook_url'] ?? '');
$instagram_url = trim($_POST['instagram_url'] ?? '');
$youtube_url = trim($_POST['youtube_url'] ?? '');
$linkedin_url = trim($_POST['linkedin_url'] ?? '');
$github_url = trim($_POST['github_url'] ?? '');

$default_meta_title = trim($_POST['default_meta_title'] ?? '');
$default_meta_description = trim($_POST['default_meta_description'] ?? '');

$google_analytics = trim($_POST['google_analytics'] ?? '');
$google_search_console = trim($_POST['google_search_console'] ?? '');

$copyright_text = trim($_POST['copyright_text'] ?? '');

/* Validation */

if ($site_name == '') {

    $_SESSION['error'] = "Website Name is required.";

    header("Location: index.php");

    exit;
}

if (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = "Invalid Email Address.";

    header("Location: index.php");

    exit;
}

/* Current Settings */

$current = $pdo->query("SELECT site_logo FROM settings WHERE id=1")->fetch();

/* Logo Upload */

$site_logo = $current['site_logo'];

if (
    isset($_FILES['site_logo']) &&
    $_FILES['site_logo']['error'] === UPLOAD_ERR_OK
) {

    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];

    $extension = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed)) {

        $_SESSION['error'] = "Invalid logo format.";

        header("Location: index.php");

        exit;
    }

    if ($_FILES['site_logo']['size'] > (2 * 1024 * 1024)) {

        $_SESSION['error'] = "Logo size must be less than 2 MB.";

        header("Location: index.php");

        exit;
    }

    $newName = 'logo_' . time() . '.' . $extension;

    $destination = __DIR__ . '/../../uploads/logo/' . $newName;

    if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $destination)) {

        if (!empty($site_logo)) {

            $oldFile = __DIR__ . '/../../uploads/logo/' . $site_logo;

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $site_logo = $newName;
    }
}

/* Update Settings */

$stmt = $pdo->prepare("
UPDATE settings SET

site_name=?,
site_tagline=?,
site_logo=?,

contact_email=?,
contact_phone=?,
whatsapp_number=?,

address=?,

facebook_url=?,
instagram_url=?,
youtube_url=?,
linkedin_url=?,
github_url=?,

default_meta_title=?,
default_meta_description=?,

google_analytics=?,
google_search_console=?,

copyright_text=?

WHERE id=1
");

$stmt->execute([

$site_name,
$site_tagline,
$site_logo,

$contact_email,
$contact_phone,
$whatsapp_number,

$address,

$facebook_url,
$instagram_url,
$youtube_url,
$linkedin_url,
$github_url,

$default_meta_title,
$default_meta_description,

$google_analytics,
$google_search_console,

$copyright_text

]);

$_SESSION['success']="Settings Updated Successfully.";

header("Location: index.php");

exit;