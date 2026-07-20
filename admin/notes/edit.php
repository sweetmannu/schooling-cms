<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/csrf.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

/* Current Note */

$stmt = $pdo->prepare("
SELECT *
FROM notes
WHERE id=?
");

$stmt->execute([$id]);

$note = $stmt->fetch();

if (!$note) {

    header("Location: index.php");
    exit;

}

/* Chapters */

$chapters = $pdo->query("
SELECT
chapters.id,
chapters.chapter_name,
subjects.subject_name
FROM chapters
INNER JOIN subjects
ON chapters.subject_id=subjects.id
WHERE chapters.status='Active'
ORDER BY subjects.subject_name,chapters.chapter_order
")->fetchAll();

$error="";

if($_SERVER['REQUEST_METHOD']=="POST"){

    verify_csrf_token($_POST['csrf_token'] ?? '');

    $chapter_id = isset($_POST['chapter_id'])
    ? (int)$_POST['chapter_id']
    : 0;

$title = trim(strip_tags($_POST['title'] ?? ''));

$slug = strtolower(trim($_POST['slug'] ?? ''));
$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
$slug = preg_replace('/-+/', '-', $slug);
$slug = trim($slug, '-');

$short_description = trim(strip_tags($_POST['short_description'] ?? ''));

$content = trim($_POST['content'] ?? '');

$youtube_url = trim($_POST['youtube_url'] ?? '');


if (!empty($youtube_url)) {

    $host = parse_url($youtube_url, PHP_URL_HOST);

    $allowedHosts = [
        'youtube.com',
        'www.youtube.com',
        'youtu.be',
        'www.youtu.be'
    ];

    if (
        !filter_var($youtube_url, FILTER_VALIDATE_URL) ||
        !in_array($host, $allowedHosts, true)
    ) {
        $error = "Enter a valid YouTube URL.";
    }

}

$meta_title = trim(strip_tags($_POST['meta_title'] ?? ''));

$meta_description = trim(strip_tags($_POST['meta_description'] ?? ''));

$featured = (($_POST['featured'] ?? 'No') === 'Yes')
    ? 'Yes'
    : 'No';

$status = (($_POST['status'] ?? 'Active') === 'Inactive')
    ? 'Inactive'
    : 'Active';

    $pdf_file=$note['pdf_file'];

    $thumbnail=$note['thumbnail'];

    if (
    empty($chapter_id) ||
    empty($title) ||
    empty($slug)
) {

    $error = "Please fill all required fields.";

}

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

    // Duplicate Slug Check

if (empty($error)) {

    $check = $pdo->prepare("
    SELECT id
    FROM notes
    WHERE slug=?
    AND id!=?
    ");

    $check->execute([$slug, $id]);

    if ($check->fetch()) {

        $error = "Slug already exists.";

    }

}

// PDF Replace

if (empty($error) && !empty($_FILES['pdf_file']['name'])) {

    if ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {

        $error = "PDF upload failed.";

    } else {

        $pdfExt = strtolower(
            pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION)
        );

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

        } elseif ($_FILES['pdf_file']['size'] > 10 * 1024 * 1024) {

            $error = "PDF size must be less than 10MB.";

        } else {

            if (
                !empty($note['pdf_file']) &&
                file_exists("../../uploads/pdf/" . $note['pdf_file'])
            ) {
                unlink("../../uploads/pdf/" . $note['pdf_file']);
            }

            $pdf_file = uniqid('pdf_', true) . ".pdf";

            if (!move_uploaded_file(
                $_FILES['pdf_file']['tmp_name'],
                "../../uploads/pdf/" . $pdf_file
            )) {

                $error = "Failed to upload PDF.";

            }

        }

    }

}

// Thumbnail Replace

if (empty($error) && !empty($_FILES['thumbnail']['name'])) {

    if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {

        $error = "Thumbnail upload failed.";

    } else {

        $imgExt = strtolower(
            pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION)
        );

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($imgExt, $allowedExt)) {

            $error = "Thumbnail must be JPG, JPEG, PNG or WEBP.";

        } elseif ($_FILES['thumbnail']['size'] > 2 * 1024 * 1024) {

            $error = "Thumbnail size must be less than 2MB.";

        } else {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $imgMime = finfo_file(
                $finfo,
                $_FILES['thumbnail']['tmp_name']
            );

            finfo_close($finfo);

            $allowedMime = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($imgMime, $allowedMime)) {

                $error = "Invalid image file.";

            } else {

                if (
                    !empty($note['thumbnail']) &&
                    file_exists("../../uploads/thumbnails/" . $note['thumbnail'])
                ) {
                    unlink("../../uploads/thumbnails/" . $note['thumbnail']);
                }

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

// Update Record

if (empty($error)) {

    $update = $pdo->prepare("
    UPDATE notes
    SET
        chapter_id=?,
        title=?,
        slug=?,
        short_description=?,
        content=?,
        pdf_file=?,
        thumbnail=?,
        youtube_url=?,
        meta_title=?,
        meta_description=?,
        featured=?,
        status=?,
        updated_at=NOW()
    WHERE id=?
    ");

    $update->execute([

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
        $status,
        $id

    ]);

    $_SESSION['success'] = "Notes Updated Successfully.";

    header("Location: index.php");
    exit;

}

}

require_once '../../includes/header.php';

?>
<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<div class="card shadow">

<div class="card-header bg-warning text-dark">

<h4 class="mb-0">
<i class="fa fa-edit"></i>
Edit Notes
</h4>

</div>

<div class="card-body">

<?php if(!empty($error)){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<form method="POST" enctype="multipart/form-data">

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

<?php foreach($chapters as $chapter){ ?>

<option
value="<?= $chapter['id']; ?>"
<?= (($_POST['chapter_id'] ?? $note['chapter_id']) == $chapter['id']) ? 'selected' : ''; ?>>

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
required
value="<?= htmlspecialchars($_POST['title'] ?? $note['title']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Slug</label>

<input
type="text"
name="slug"
id="slug"
class="form-control"
required
value="<?= htmlspecialchars($_POST['slug'] ?? $note['slug']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Replace PDF</label>

<input
type="file"
name="pdf_file"
class="form-control"
accept=".pdf">

<?php if(!empty($note['pdf_file'])){ ?>

<div class="mt-2">

<a
href="../../uploads/pdf/<?= urlencode($note['pdf_file']); ?>"
target="_blank"
class="btn btn-sm btn-primary">

Current PDF

</a>

</div>

<?php } ?>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Replace Thumbnail</label>

<input
type="file"
name="thumbnail"
class="form-control"
accept=".jpg,.jpeg,.png,.webp">

<?php if(!empty($note['thumbnail'])){ ?>

<div class="mt-2">

<img
src="../../uploads/thumbnails/<?= htmlspecialchars($note['thumbnail']); ?>"
style="max-width:120px;"
class="img-thumbnail">

</div>

<?php } ?>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">YouTube URL</label>

<input
type="url"
name="youtube_url"
class="form-control"
value="<?= htmlspecialchars($_POST['youtube_url'] ?? $note['youtube_url']); ?>">

</div>

<div class="col-12 mb-3">

<label class="form-label">Short Description</label>

<textarea
name="short_description"
class="form-control"
rows="3"><?= htmlspecialchars($_POST['short_description'] ?? $note['short_description']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Content</label>

<textarea
name="content"
id="content"
class="form-control"
rows="8"><?= htmlspecialchars($_POST['content'] ?? $note['content']); ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Meta Title</label>

<input
type="text"
name="meta_title"
class="form-control"
value="<?= htmlspecialchars($_POST['meta_title'] ?? $note['meta_title']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Featured</label>

<select
name="featured"
class="form-select">

<option value="No"
<?= (($_POST['featured'] ?? $note['featured'])=='No')?'selected':''; ?>>
No
</option>

<option value="Yes"
<?= (($_POST['featured'] ?? $note['featured'])=='Yes')?'selected':''; ?>>
Yes
</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">Meta Description</label>

<textarea
name="meta_description"
class="form-control"
rows="3"><?= htmlspecialchars($_POST['meta_description'] ?? $note['meta_description']); ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Status</label>

<select
name="status"
class="form-select">

<option value="Active"
<?= (($_POST['status'] ?? $note['status'])=='Active')?'selected':''; ?>>
Active
</option>

<option value="Inactive"
<?= (($_POST['status'] ?? $note['status'])=='Inactive')?'selected':''; ?>>
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

Update Notes

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

<script>
document.getElementById('title').addEventListener('input', function () {

let slug = this.value
.toLowerCase()
.trim()
.replace(/[^a-z0-9]+/g,'-')
.replace(/^-+|-+$/g,'');

document.getElementById('slug').value = slug;

});
</script>

<?php require_once '../../includes/footer.php'; ?>