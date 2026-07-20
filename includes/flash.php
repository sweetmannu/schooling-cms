<?php

if (isset($_SESSION['success'])) { ?>

<div class="alert alert-success alert-dismissible fade show" role="alert">

    <i class="fa fa-check-circle me-2"></i>

    <?= htmlspecialchars($_SESSION['success']); ?>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

</div>

<?php
unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) { ?>

<div class="alert alert-danger alert-dismissible fade show" role="alert">

    <i class="fa fa-times-circle me-2"></i>

    <?= htmlspecialchars($_SESSION['error']); ?>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

</div>

<?php
unset($_SESSION['error']);
}
?>