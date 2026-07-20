<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

/* Dashboard Counts */

$totalCategories = $pdo->query("
SELECT COUNT(*) FROM categories
")->fetchColumn();

$totalSubjects = $pdo->query("
SELECT COUNT(*) FROM subjects
")->fetchColumn();

$totalChapters = $pdo->query("
SELECT COUNT(*) FROM chapters
")->fetchColumn();

$totalNotes = $pdo->query("
SELECT COUNT(*) FROM notes
")->fetchColumn();

/* Users Table Future Ready */

$totalUsers = 1;

require_once '../includes/header.php';

?>

<div class="d-flex">

<?php require_once '../includes/sidebar.php'; ?>

<div class="container-fluid p-4">

<h2 class="mb-4">
    <i class="fa fa-gauge"></i> Dashboard
</h2>

<?php require_once '../includes/flash.php'; ?>

<div class="row g-4">

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow bg-primary text-white">

<div class="card-body">

<h6>Total Categories</h6>

<h2><?= $totalCategories; ?></h2>

<i class="fa fa-folder fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow bg-success text-white">

<div class="card-body">

<h6>Total Subjects</h6>

<h2><?= $totalSubjects; ?></h2>

<i class="fa fa-book fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow bg-warning text-dark">

<div class="card-body">

<h6>Total Chapters</h6>

<h2><?= $totalChapters; ?></h2>

<i class="fa fa-list fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card border-0 shadow bg-danger text-white">

<div class="card-body">

<h6>Total Notes</h6>

<h2><?= $totalNotes; ?></h2>

<i class="fa fa-file fa-2x float-end"></i>

</div>

</div>

</div>

</div>

<hr class="my-5">

<div class="row">

<div class="col-md-12">

<div class="card shadow">

<div class="card-header bg-dark text-white">

Quick Actions

</div>

<div class="card-body">

<a href="categories/" class="btn btn-primary m-2">

<i class="fa fa-folder"></i>

Categories

</a>

<a href="subjects/" class="btn btn-success m-2">

<i class="fa fa-book"></i>

Subjects

</a>

<a href="chapters/" class="btn btn-warning m-2">

<i class="fa fa-list"></i>

Chapters

</a>

<a href="notes/" class="btn btn-danger m-2">

<i class="fa fa-file"></i>

Notes

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<?php require_once '../includes/footer.php'; ?>