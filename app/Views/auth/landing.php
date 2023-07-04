<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>P2P Web Copier</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="P2P Web Copier" name="description" />
        <meta content="Niccher Inc" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico')?>">

        <!-- App css -->
        <link href="<?php echo base_url('assets/css/icons.min.css')?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/css/app.min.css')?>" rel="stylesheet" type="text/css" id="light-style" />
        <link href="<?php echo base_url('assets/css/app-dark.min.css')?>" rel="stylesheet" type="text/css" id="dark-style" />
    </head>
    <body class="loading" data-layout-config='{"darkMode":false}'>
        <!-- NAVBAR START -->
        <nav class="navbar navbar-expand-lg py-lg-3 navbar-dark bg-secondary">
            <div class="container">
                <!-- logo -->
                <a href="<?php echo base_url('')?>" class="navbar-brand me-lg-5">
                    <img src="<?php echo base_url('assets/images/logo.png')?>" alt="" height="18">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="mdi mdi-menu"></i>
                </button>
                <!-- menus -->
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <!-- left menu -->
                    <ul class="navbar-nav me-auto align-items-center">
                        <li class="nav-item mx-lg-1">
                            <a class="nav-link" href="#section_faqs">FAQs</a>
                        </li>
                        <li class="nav-item mx-lg-1">
                            <a class="nav-link" href="#section_contact">Contact Me</a>
                        </li>
                    </ul>
                    <!-- right menu -->
                    <ul class="navbar-nav ms-auto align-items-center">
                    </ul>
                </div>
            </div>
        </nav>
        <!-- NAVBAR END -->
        <!-- START HERO -->
        <section class="bg-secondary pt-2 pb-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="mt-md-4">
                            <h2 class="text-white fw-normal mb-2 mt-1 hero-title">
                                P2P Copier
                            </h2>
                            <p class="mb-4 font-16 text-white-50">Hyper is a fully featured dashboard and admin template
                                comes with tones of well designed UI elements, components, widgets and pages.
                            </p>
                            <a href="#"  class="btn btn-success">
                                Get Started <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-8 bg-secondary">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h1 class="mt-0"><i class="mdi mdi-frequently-asked-questions"></i></h1>
                                    <h3>Get Started <span class="text-primary">Questions</span></h3>
                                    <p class="text-muted mt-2">A little description is here.
                                    </p>
                                </div>
                                <ul class="nav nav-tabs nav-bordered mb-3">
                                    <li class="nav-item">
                                        <a href="#part_Code" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                            Code
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#part_QR" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                            Scan QR
                                        </a>
                                    </li>
                                </ul>
                                <!-- end nav-->
                                <div class="tab-content">
                                    <div class="tab-pane show" id="part_Code">
                                        <div class="row">
                                            <div class="card-body text-center">
                                                <i class="card-pricing-icon dripicons-briefcase text-primary"></i>
                                                <h2 class="card-pricing-price">
                                                    <span class="text-primary">CODE HERE</span>
                                                    <span>/ License</span>
                                                </h2>
                                                <ul class="card-pricing-features">
                                                    <li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end preview-->
                                    <div class="tab-pane active" id="part_QR">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <?php
                                                    $qr_url =  base_url($filepath);
                                                ?>
                                                <img src="<?php echo base_url($filepath); ?>" class="img-fluid" alt="Here">
                                            </div>
                                            <div class="col-lg-6 pt-4">
                                                <h3 class="fw-normal">Inbuilt applications and pages</h3>
                                                <p class="text-muted mt-3">Hyper comes with a variety of ready-to-use the development</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end preview code-->
                                </div>
                                <!-- end tab-content-->
                            </div>
                            <!-- end card-body -->
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- END HERO -->
        <!-- START FAQ -->
        <section class="py-3" id="section_faqs">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h1 class="mt-0"><i class="mdi mdi-frequently-asked-questions"></i></h1>
                            <h3>Frequently Asked <span class="text-primary">Questions</span></h3>
                            <p class="text-muted mt-2">Here are some of the basic types of questions for our customers. For more
                                <br>information please contact us.
                            </p>
                            <button type="button" class="btn btn-success btn-sm mt-2"><i class="mdi mdi-email-outline me-1"></i> Email us your question</button>
                            <button type="button" class="btn btn-info btn-sm mt-2 ms-1"><i class="mdi mdi-twitter me-1"></i> Send us a tweet</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-5 offset-lg-1">
                        <!-- Question/Answer -->
                        <div>
                            <div class="faq-question-q-box">Q.</div>
                            <h4 class="faq-question text-body">Can I use this template for my client?</h4>
                            <p class="faq-answer mb-4 pb-1 text-muted">Yup, the marketplace license allows you to use this theme
                                in any end products.
                                For more information on licenses, please refere <a href="https://themes.getbootstrap.com/licenses/" target="_blank">here</a>.
                            </p>
                        </div>
                        <!-- Question/Answer -->
                        <div>
                            <div class="faq-question-q-box">Q.</div>
                            <h4 class="faq-question text-body">How do I get help with the theme?</h4>
                            <p class="faq-answer mb-4 pb-1 text-muted">Use our dedicated support email (support@coderthemes.com) to send your issues or feedback. We are here to help anytime.</p>
                        </div>
                    </div>
                    <!--/col-lg-5 -->
                    <div class="col-lg-5">
                        <!-- Question/Answer -->
                        <div>
                            <div class="faq-question-q-box">Q.</div>
                            <h4 class="faq-question text-body">Can this theme work with Wordpress?</h4>
                            <p class="faq-answer mb-4 pb-1 text-muted">No. This is a HTML template. It won't directly with
                                wordpress, though you can convert this into wordpress compatible theme.
                            </p>
                        </div>
                        <!-- Question/Answer -->
                        <div>
                            <div class="faq-question-q-box">Q.</div>
                            <h4 class="faq-question text-body">Will you regularly give updates of Hyper?</h4>
                            <p class="faq-answer mb-4 pb-1 text-muted">Yes, We will update the Hyper regularly. All the
                                future updates would be available without any cost.
                            </p>
                        </div>
                    </div>
                    <!--/col-lg-5-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-->
        </section>
        <!-- END FAQ -->
        <!-- START CONTACT -->
        <section class="py-3 bg-light-lighten border-top border-bottom border-light"  id="section_contact">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h3>Get In <span class="text-primary">Touch</span></h3>
                            <p class="text-muted mt-2">Please fill out the following form and we will get back to you shortly. For more
                                <br>information please contact us.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-md-4">
                        <p class="text-muted"><span class="fw-bold">Customer Support:</span><br> <span class="d-block mt-1">+1 234 56 7894</span></p>
                        <p class="text-muted mt-4"><span class="fw-bold">Email Address:</span><br> <span class="d-block mt-1">info@gmail.com</span></p>
                        <p class="text-muted mt-4"><span class="fw-bold">Office Address:</span><br> <span class="d-block mt-1">4461 Cedar Street Moro, AR 72368</span></p>
                        <p class="text-muted mt-4"><span class="fw-bold">Office Time:</span><br> <span class="d-block mt-1">9:00AM To 6:00PM</span></p>
                    </div>
                    <div class="col-md-8">
                        <form>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="fullname" class="form-label">Your Name</label>
                                        <input class="form-control form-control-light" type="text" id="fullname" placeholder="Name...">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label for="emailaddress" class="form-label">Your Email</label>
                                        <input class="form-control form-control-light" type="email" required="" id="emailaddress" placeholder="Enter you email...">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="subject" class="form-label">Your Subject</label>
                                        <input class="form-control form-control-light" type="text" id="subject" placeholder="Enter subject...">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-lg-12">
                                    <div class="mb-2">
                                        <label for="comments" class="form-label">Message</label>
                                        <textarea id="comments" rows="4" class="form-control form-control-light" placeholder="Type your message here..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-end">
                                    <button class="btn btn-primary">Send a Message <i
                                                class="mdi mdi-telegram ms-1"></i> </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- END CONTACT -->
        <!-- START FOOTER -->
        <footer class="bg-dark py-3">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mt-2">
                            <p class="text-muted mt-1 text-center mb-1">
                                <script>document.write(new Date().getFullYear())</script> © Niccher Inc
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END FOOTER -->
        <script src="<?php echo base_url('assets/js/vendor.min.js')?>"></script>
        <script src="<?php echo base_url('assets/js/app.min.js')?>"></script>
    </body>
</html>