
            <!-- content -->
            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <script>document.write(new Date().getFullYear())</script> © Niccher Inc
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-end footer-links d-none d-md-block">
                                <a href="javascript: void(0);">About</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->
        <!-- bundle -->
        <script src="<?php echo base_url('assets/js/jquery-3.7.0.min.js')?>"></script>

        <script src="<?php echo base_url('assets/js/vendor.min.js')?>"></script>
        <script src="<?php echo base_url('assets/js/app.min.js')?>"></script>

        <script src="<?php echo base_url('assets/summernote/summernote.min.js')?>"></script>

        <!-- Initialize Quill editor -->
        <script>
            $(document).ready(function() {
                $('#please_type_here').summernote({
                    placeholder: 'Please type here',
                    tabsize: 2,
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    focus: true
                });
            });
        </script>

    </body>
</html>
