<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';

$chapters = $pdo->query("
SELECT
chapters.id,
chapters.chapter_name,
subjects.subject_name
FROM chapters
INNER JOIN subjects
ON chapters.subject_id = subjects.id
WHERE chapters.status='Active'
ORDER BY subjects.subject_name, chapters.chapter_order
")->fetchAll();

$error = "";

if ($_SERVER['REQUEST_METHOD']=="POST"){

        verify_csrf_token($_POST['csrf_token'] ?? '');

    $chapter_id = isset($_POST['chapter_id'])
    ? (int)$_POST['chapter_id']
    : 0;

    $title = trim(strip_tags($_POST['title'] ?? ''));

    $slug = strtolower(trim($_POST['slug'] ?? ''));
    $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug);
    $slug = trim($slug, '-');

    $short_description = trim(strip_tags($_POST['short_description'] ?? ''));

    $content = trim($_POST['content'] ?? '');

    $youtube_url = trim($_POST['youtube_url'] ?? '');

    $meta_title = trim(strip_tags($_POST['meta_title'] ?? ''));

    $meta_description = trim(strip_tags($_POST['meta_description'] ?? ''));

    $featured = (($_POST['featured'] ?? 'No') === 'Yes')
    ? 'Yes'
    : 'No';

    $status = (($_POST['status'] ?? 'Active') === 'Inactive')
    ? 'Inactive'
    : 'Active';

    /* Required Validation */

if (
    empty($chapter_id) ||
    empty($title) ||
    empty($slug)
) {

    $error = "Please fill all required fields.";

}

/* Validate Chapter */

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM chapters
    WHERE id=?
    AND status='Active'
    ");

    $check->execute([$chapter_id]);

    if (!$check->fetch()) {

        $error = "Invalid chapter selected.";

    }

}

/* Duplicate Title Check */

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM notes
    WHERE title=?
    ");

    $check->execute([$title]);

    if ($check->fetch()) {

        $error = "Title already exists.";

    }

}

/* Duplicate Slug Check */

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM notes
    WHERE slug=?
    ");

    $check->execute([$slug]);

    if ($check->fetch()) {

        $error = "Slug already exists.";

    }

}

    $pdf_file = "";

    $thumbnail = "";

    /* YouTube URL Validation */

if (
    empty($error) &&
    !empty($youtube_url) &&
    !filter_var($youtube_url, FILTER_VALIDATE_URL)
) {

    $error = "Please enter a valid YouTube URL.";

}

    // PDF Upload

if (empty($error) && !empty($_FILES['pdf_file']['name'])) {

    $pdfExt = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

$pdfMime = finfo_file(
    $finfo,
    $_FILES['pdf_file']['tmp_name']
);

finfo_close($finfo);

    if (
        $pdfExt !== 'pdf' ||
        $pdfMime !== 'application/pdf'
    ) {

        $error = "Only valid PDF files are allowed.";

    } else {

        $pdf_file = uniqid('pdf_', true) . '.pdf';

if ($_FILES['pdf_file']['size'] > 10 * 1024 * 1024) {

    $error = "PDF size must be less than 10MB.";

} elseif (!move_uploaded_file(

    $_FILES['pdf_file']['tmp_name'],
    "../../uploads/pdf/" . $pdf_file

)) {

    $error = "Failed to upload PDF.";

}

    }

}

    // Thumbnail Upload

if (empty($error) && !empty($_FILES['thumbnail']['name'])) {

    if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {

        $error = "Thumbnail upload failed.";

    } else {

        $imgExt = strtolower(
            pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($imgExt, $allowed)) {

            $error = "Thumbnail must be JPG, JPEG, PNG or WEBP.";

        } elseif ($_FILES['thumbnail']['size'] > 2 * 1024 * 1024) {

            $error = "Thumbnail size must be less than 2MB.";

        } else {

            $imgMime = mime_content_type($_FILES['thumbnail']['tmp_name']);

            $allowedMime = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($imgMime, $allowedMime)) {

                $error = "Invalid image file.";

            } else {

                $thumbnail = uniqid('thumb_', true) . "." . $imgExt;

                if (!move_uploaded_file(
                    $_FILES['thumbnail']['tmp_name'],
                    "../../uploads/thumbnails/" . $thumbnail
                )) {

                    $error = "Failed to upload thumbnail.";

                }

            }

        }

    }

}

// Save Record
if (empty($error)) {

    $stmt = $pdo->prepare("
    INSERT INTO notes
    (
        chapter_id,
        title,
        slug,
        short_description,
        content,
        pdf_file,
        thumbnail,
        youtube_url,
        meta_title,
        meta_description,
        featured,
        status
    )
    VALUES
    (?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $chapter_id,
        $title,
        $slug,
        $short_description,
        $content,
        $pdf_file,
        $thumbnail,
        $youtube_url,
        $meta_title,
        $meta_description,
        $featured,
        $status
    ]);

    $_SESSION['success'] = "Notes Added Successfully.";

    header("Location: index.php");
    exit;
}

}

require_once '../../includes/header.php';

?><div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">
<i class="fa fa-file-alt"></i>
Add Notes
</h4>

</div>

<div class="card-body">

<?php if(!empty($error)){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<form method="POST" enctype="multipart/form-data" autocomplete="off">

<input
type="hidden"
name="csrf_token"
value="<?= csrf_token(); ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Chapter</label>

<select
name="chapter_id"
class="form-select"
required>

<option value="">Select Chapter</option>

<?php foreach($chapters as $chapter){ ?>

<option
value="<?= $chapter['id']; ?>"
<?= (($_POST['chapter_id'] ?? '') == $chapter['id']) ? 'selected' : ''; ?>>

<?= htmlspecialchars($chapter['subject_name']); ?>

→

<?= htmlspecialchars($chapter['chapter_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Title</label>

<input
type="text"
name="title"
id="title"
class="form-control"
value="<?= htmlspecialchars($_POST['title'] ?? ''); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Slug</label>

<input
type="text"
name="slug"
id="slug"
class="form-control"
value="<?= htmlspecialchars($_POST['slug'] ?? ''); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">PDF File</label>

<input
type="file"
name="pdf_file"
class="form-control"
accept=".pdf">

<small class="text-muted">
Only PDF files allowed
</small>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Thumbnail</label>

<input
type="file"
name="thumbnail"
class="form-control"
accept=".jpg,.jpeg,.png,.webp">

<small class="text-muted">
JPG, PNG, WEBP
</small>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

YouTube URL

</label>

<input
type="url"
name="youtube_url"
class="form-control"
value="<?= htmlspecialchars($_POST['youtube_url'] ?? ''); ?>">

</div>

<div class="col-12 mb-3">

<label class="form-label">

Short Description

</label>

<textarea
name="short_description"
class="form-control"
rows="3"><?= htmlspecialchars($_POST['short_description'] ?? ''); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Content

</label>

<textarea
name="content"
id="content"
class="form-control"
rows="8"><?= htmlspecialchars($_POST['content'] ?? ''); ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Meta Title

</label>

<input
type="text"
name="meta_title"
class="form-control"
value="<?= htmlspecialchars($_POST['meta_title'] ?? ''); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Featured

</label>

<select
name="featured"
class="form-select">

<option
value="No"
<?= (($_POST['featured'] ?? 'No') == 'No') ? 'selected' : ''; ?>>
No
</option>

<option
value="Yes"
<?= (($_POST['featured'] ?? '') == 'Yes') ? 'selected' : ''; ?>>
Yes
</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Meta Description

</label>

<textarea
name="meta_description"
class="form-control"
rows="3"><?= htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option
value="Active"
<?= (($_POST['status'] ?? 'Active') == 'Active') ? 'selected' : ''; ?>>
Active
</option>

<option
value="Inactive"
<?= (($_POST['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>
Inactive
</option>

</select>

</div>

</div>

<div class="mt-3">

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Save Notes

</button>

<a
href="index.php"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>


<script>

// Auto Slug

document.getElementById('title').addEventListener('input', function () {

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    document.getElementById('slug').value = slug;

});

// TinyMCE

tinymce.init({
    license_key: 'gpl',

    selector: '#content',

    height: 500,

    menubar: true,

    plugins: [
        'advlist','autolink','lists','link','image',
        'table','code','fullscreen','preview',
        'searchreplace','wordcount'
    ],

    toolbar:
        'undo redo | blocks | bold italic underline | ' +
        'alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist | link image table | ' +
        'code preview fullscreen'

});

</script>