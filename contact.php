<?php

require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

$pageTitle = 'Contact Us - ' . APP_NAME;

$pageDescription =
    'Contact ' . APP_NAME .
    ' for questions, feedback, support, or educational inquiries.';

$robots = 'index,follow';

require_once __DIR__ . '/includes/frontend/header.php';
require_once __DIR__ . '/includes/frontend/navbar.php';

?>

<main>

    <!-- ======================================================
         Page Header
    ======================================================= -->

    <section class="py-5 bg-light border-bottom">

        <div class="container">

            <div class="text-center">

                <h1 class="fw-bold mb-3">
                    Contact Us
                </h1>

                <p class="lead text-muted mb-0">

                    Have a question, suggestion, or need help?
                    We would be happy to hear from you.

                </p>

            </div>

        </div>

    </section>


    <!-- ======================================================
         Breadcrumb
    ======================================================= -->

    <section class="py-3">

        <div class="container">

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb mb-0">

                    <li class="breadcrumb-item">

                        <a href="<?= htmlspecialchars(
                            rtrim(APP_URL, '/') . '/',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>">

                            Home

                        </a>

                    </li>

                    <li
                        class="breadcrumb-item active"
                        aria-current="page"
                    >
                        Contact
                    </li>

                </ol>

            </nav>

        </div>

    </section>


    <!-- ======================================================
         Contact Section
    ======================================================= -->

    <section class="py-5">

        <div class="container">

            <div class="row g-4">

                <!-- Contact Information -->

                <div class="col-lg-5">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4 p-md-5">

                            <h2 class="h3 fw-bold mb-4">
                                Get in Touch
                            </h2>

                            <p class="text-muted mb-4">

                                You can contact us for general questions,
                                educational support, feedback, or suggestions.

                            </p>

                            <div class="d-flex mb-4">

                                <div class="me-3 fs-4">

                                    <i class="fa fa-envelope"></i>

                                </div>

                                <div>

                                    <h3 class="h6 fw-bold mb-1">
                                        Email
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Contact email can be added from
                                        website settings.
                                    </p>

                                </div>

                            </div>

                            <div class="d-flex mb-4">

                                <div class="me-3 fs-4">

                                    <i class="fa fa-phone"></i>

                                </div>

                                <div>

                                    <h3 class="h6 fw-bold mb-1">
                                        Phone
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Contact number can be added from
                                        website settings.
                                    </p>

                                </div>

                            </div>

                            <div class="d-flex">

                                <div class="me-3 fs-4">

                                    <i class="fa fa-map-marker-alt"></i>

                                </div>

                                <div>

                                    <h3 class="h6 fw-bold mb-1">
                                        Address
                                    </h3>

                                    <p class="text-muted mb-0">
                                        Business address can be managed
                                        through website settings.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Contact Form -->

                <div class="col-lg-7">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-4 p-md-5">

                            <h2 class="h3 fw-bold mb-4">
                                Send a Message
                            </h2>

                            <div class="alert alert-info">

                                <i class="fa fa-info-circle me-1"></i>

                                The contact form interface is ready.
                                Message submission will be connected in
                                the next development step.

                            </div>

                            <form method="POST" action="">

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <label
                                            for="name"
                                            class="form-label"
                                        >
                                            Name
                                        </label>

                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control"
                                            maxlength="100"
                                            required
                                        >

                                    </div>

                                    <div class="col-md-6">

                                        <label
                                            for="email"
                                            class="form-label"
                                        >
                                            Email
                                        </label>

                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            class="form-control"
                                            maxlength="190"
                                            required
                                        >

                                    </div>

                                    <div class="col-12">

                                        <label
                                            for="subject"
                                            class="form-label"
                                        >
                                            Subject
                                        </label>

                                        <input
                                            type="text"
                                            id="subject"
                                            name="subject"
                                            class="form-control"
                                            maxlength="200"
                                            required
                                        >

                                    </div>

                                    <div class="col-12">

                                        <label
                                            for="message"
                                            class="form-label"
                                        >
                                            Message
                                        </label>

                                        <textarea
                                            id="message"
                                            name="message"
                                            class="form-control"
                                            rows="6"
                                            maxlength="5000"
                                            required
                                        ></textarea>

                                    </div>

                                    <div class="col-12">

                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                            disabled
                                        >

                                            <i class="fa fa-paper-plane me-1"></i>

                                            Send Message

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/includes/frontend/footer.php';

?>