<?php

require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch();

require_once '../../includes/header.php';
?>

<div class="d-flex">

<?php require_once '../../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<h2 class="mb-4">
<i class="fa fa-cog"></i> Website Settings
</h2>

<?php require_once '../../includes/flash.php'; ?>

<div class="card shadow">

<div class="card-body">

<form action="update.php" method="POST" enctype="multipart/form-data">

<?php require_once '../../includes/csrf.php'; ?>


<input
type="hidden"
name="csrf_token"
value="<?= csrf_token(); ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Website Name</label>

<input
type="text"
name="site_name"
class="form-control"
value="<?= htmlspecialchars($settings['site_name']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Tagline</label>

<input
type="text"
name="site_tagline"
class="form-control"
value="<?= htmlspecialchars($settings['site_tagline']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Contact Email</label>

<input
type="email"
name="contact_email"
class="form-control"
value="<?= htmlspecialchars($settings['contact_email']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Phone</label>

<input
type="text"
name="contact_phone"
class="form-control"
value="<?= htmlspecialchars($settings['contact_phone']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">WhatsApp</label>

<input
type="text"
name="whatsapp_number"
class="form-control"
value="<?= htmlspecialchars($settings['whatsapp_number']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Facebook URL</label>

<input
type="text"
name="facebook_url"
class="form-control"
value="<?= htmlspecialchars($settings['facebook_url']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Instagram URL</label>

<input
type="text"
name="instagram_url"
class="form-control"
value="<?= htmlspecialchars($settings['instagram_url']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">YouTube URL</label>

<input
type="text"
name="youtube_url"
class="form-control"
value="<?= htmlspecialchars($settings['youtube_url']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">LinkedIn URL</label>

<input
type="text"
name="linkedin_url"
class="form-control"
value="<?= htmlspecialchars($settings['linkedin_url']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">GitHub URL</label>

<input
type="text"
name="github_url"
class="form-control"
value="<?= htmlspecialchars($settings['github_url']); ?>">

</div>

<div class="col-12 mb-3">

<label class="form-label">Address</label>

<textarea
name="address"
rows="3"
class="form-control"><?= htmlspecialchars($settings['address']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Default Meta Title</label>

<input
type="text"
name="default_meta_title"
class="form-control"
value="<?= htmlspecialchars($settings['default_meta_title']); ?>">

</div>

<div class="col-12 mb-3">

<label class="form-label">Default Meta Description</label>

<textarea
name="default_meta_description"
rows="4"
class="form-control"><?= htmlspecialchars($settings['default_meta_description']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Google Analytics</label>

<textarea
name="google_analytics"
rows="4"
class="form-control"><?= htmlspecialchars($settings['google_analytics']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Google Search Console</label>

<textarea
name="google_search_console"
rows="3"
class="form-control"><?= htmlspecialchars($settings['google_search_console']); ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Copyright</label>

<input
type="text"
name="copyright_text"
class="form-control"
value="<?= htmlspecialchars($settings['copyright_text']); ?>">

</div>

<hr class="my-4">

<h4 class="mb-4">

<i class="fa fa-image"></i>

Website Branding

</h4>

<div class="col-md-6 mb-4">

<label class="form-label fw-bold">

Website Logo

</label>

<?php if(!empty($settings['site_logo'])){ ?>

<div class="mb-3">

<img
src="<?= APP_URL; ?>/uploads/logo/<?= htmlspecialchars($settings['site_logo']); ?>"
class="img-thumbnail"
style="max-height:80px;">

</div>

<?php } ?>

<input
type="file"
name="site_logo"
class="form-control"
accept=".png,.jpg,.jpeg,.webp,.svg">

<small class="text-muted">

Allowed: PNG, JPG, JPEG, WEBP, SVG

</small>

</div>

<div class="col-md-6 mb-4">

<label class="form-label fw-bold">

Website Favicon

</label>

<?php if(!empty($settings['site_favicon'])){ ?>

<div class="mb-3">

<img
src="<?= APP_URL; ?>/uploads/logo/favicon/<?= htmlspecialchars($settings['site_favicon']); ?>"
class="img-thumbnail"
style="max-height:48px;">

</div>

<?php } ?>

<input
type="file"
name="site_favicon"
class="form-control"
accept=".png,.ico">

<small class="text-muted">

Allowed: PNG, ICO

</small>

</div>

<div class="text-end">

<button
type="submit"
class="btn btn-primary">

<i class="fa fa-save"></i>

Save Settings

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

<?php require_once '../../includes/footer.php'; ?>