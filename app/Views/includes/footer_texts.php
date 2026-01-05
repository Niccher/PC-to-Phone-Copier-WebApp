
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

        <!-- Initialize Summernote editor and text functionality -->
        <script>
            $(document).ready(function() {
                // Initialize Summernote editor
                $('#please_type_here').summernote({
                    placeholder: 'Please type here...',
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

                // Save text functionality
                $('#save_text_btn').on('click', function() {
                    var textContent = $('#please_type_here').summernote('code');
                    var textTitle = $('#text_title').val().trim();

                    if (textContent.trim() === '' || textContent === '<p><br></p>') {
                        alert('Please enter some text to save.');
                        return;
                    }

                    // Show loading state
                    var $btn = $(this);
                    var originalText = $btn.html();
                    $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

                    $.ajax({
                        url: '<?php echo base_url("text/save"); ?>',
                        type: 'POST',
                        data: {
                            text_content: textContent,
                            text_title: textTitle
                        },
                        success: function(response) {
                            if (response.status == 1) {
                                // Success - clear form and refresh lists
                                $('#please_type_here').summernote('code', '');
                                $('#text_title').val('');
                                refreshTextLists();
                                alert('Text saved successfully!');
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function() {
                            alert('Error saving text. Please try again.');
                        },
                        complete: function() {
                            // Reset button state
                            $btn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // Clear text functionality
                $('#clear_text_btn').on('click', function() {
                    if (confirm('Are you sure you want to clear the text?')) {
                        $('#please_type_here').summernote('code', '');
                        $('#text_title').val('');
                    }
                });

                // Copy to clipboard functionality
                $(document).on('click', '.copy-text-btn', function() {
                    var textToCopy = $(this).data('text');

                    // Use the Clipboard API if available
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(textToCopy).then(function() {
                            showCopySuccess();
                        }).catch(function() {
                            fallbackCopyTextToClipboard(textToCopy);
                        });
                    } else {
                        fallbackCopyTextToClipboard(textToCopy);
                    }
                });

                // Fallback copy function for older browsers
                function fallbackCopyTextToClipboard(text) {
                    var textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "fixed";
                    textArea.style.left = "-999999px";
                    textArea.style.top = "-999999px";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        var successful = document.execCommand('copy');
                        if (successful) {
                            showCopySuccess();
                        } else {
                            alert('Failed to copy text to clipboard');
                        }
                    } catch (err) {
                        alert('Failed to copy text to clipboard');
                    }

                    document.body.removeChild(textArea);
                }

                // Show copy success message
                function showCopySuccess() {
                    // Create a temporary success message
                    var $msg = $('<div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">')
                        .html('<i class="mdi mdi-check-circle"></i> Text copied to clipboard!')
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');

                    $('body').append($msg);

                    // Auto remove after 3 seconds
                    setTimeout(function() {
                        $msg.alert('close');
                    }, 3000);
                }

                // Function to refresh text lists
                function refreshTextLists() {
                    $.ajax({
                        url: '<?php echo base_url("home/texts"); ?>',
                        type: 'GET',
                        success: function(response) {
                            // This is a simple refresh - in a real implementation,
                            // you'd want to extract just the list parts from the response
                            location.reload();
                        }
                    });
                }
            });
        </script>

    </body>
</html>
