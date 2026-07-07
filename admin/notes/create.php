<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

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

    $chapter_id = (int)$_POST['chapter_id'];

    $title = trim($_POST['title']);

    $slug = strtolower(trim($_POST['slug']));

    $short_description = trim($_POST['short_description']);

    $content = trim($_POST['content']);

    $youtube_url = trim($_POST['youtube_url']);

    $meta_title = trim($_POST['meta_title']);

    $meta_description = trim($_POST['meta_description']);

    $featured = $_POST['featured'];

    $status = $_POST['status'];

    $pdf_file = "";

    $thumbnail = "";

    // PDF Upload
if (!empty($_FILES['pdf_file']['name'])) {

    $pdfExt = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));

    if ($pdfExt != 'pdf') {
        $error = "Only PDF files are allowed.";
    } else {

        $pdf_file = time() . "_pdf." . $pdfExt;

        move_uploaded_file(
            $_FILES['pdf_file']['tmp_name'],
            "../../uploads/pdf/" . $pdf_file
        );
    }
}

// Thumbnail Upload
if (empty($error) && !empty($_FILES['thumbnail']['name'])) {

    $imgExt = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($imgExt, $allowed)) {

        $error = "Thumbnail must be JPG, JPEG, PNG or WEBP.";

    } else {

        $thumbnail = time() . "_thumb." . $imgExt;

        move_uploaded_file(
            $_FILES['thumbnail']['tmp_name'],
            "../../uploads/thumbnails/" . $thumbnail
        );

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

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Chapter</label>

<select
name="chapter_id"
class="form-select"
required>

<option value="">Select Chapter</option>

<?php foreach($chapters as $chapter){ ?>

<option value="<?= $chapter['id']; ?>">

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
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Slug</label>

<input
type="text"
name="slug"
id="slug"
class="form-control"
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
class="form-control">

</div>

<div class="col-12 mb-3">

<label class="form-label">

Short Description

</label>

<textarea
name="short_description"
class="form-control"
rows="3"></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Content

</label>

<textarea
name="content"
id="content"
class="form-control"
rows="8"></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Meta Title

</label>

<input
type="text"
name="meta_title"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Featured

</label>

<select
name="featured"
class="form-select">

<option value="No">No</option>

<option value="Yes">Yes</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Meta Description

</label>

<textarea
name="meta_description"
class="form-control"
rows="3"></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option value="Active">Active</option>

<option value="Inactive">Inactive</option>

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
<script>

document.getElementById('title').addEventListener('keyup', function () {

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    document.getElementById('slug').value = slug;

});

</script>

<?php require_once '../../includes/footer.php'; ?>