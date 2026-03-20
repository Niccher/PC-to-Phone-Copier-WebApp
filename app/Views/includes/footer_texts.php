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
<script src="<?php echo base_url('assets/js/vendor.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/app.min.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Summernote Lite -->
<link href="<?php echo base_url('assets/summernote/summernote-lite.min.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/summernote/summernote-lite.min.js')?>"></script>

<!-- Dropzone -->
<link href="<?php echo base_url('assets/dropzone/dropzone.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/dropzone/dropzone.js')?>"></script>
<script>Dropzone.autoDiscover = false;</script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Initialize Summernote editor and text functionality -->

<?php include 'upload_modal.php'; ?>
<?php include 'qr_modal.php'; ?>

<script>
    $(document).ready(function () {
        // QR Code Generation for Pairing (Main Sidebar)
        var pairQrElement = document.getElementById('pair-qr');
        if (pairQrElement) {
            setTimeout(function () {
                pairQrElement.innerHTML = '';
                new QRCode(pairQrElement, {
                    text: window.location.origin,
                    width: 150,
                    height: 150,
                    colorDark: "#313a46",
                    colorLight: "#f1f3fa",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }, 800);
        }



        // Global Modal Summernote
        if ($('#modal_summernote').length) {
            $('#modal_summernote').summernote({ height: 200, placeholder: 'Type your text here...' });
        }

        // Save text from modal
        $('#modal_save_text_btn').on('click', function () {
            var content = $('#modal_summernote').summernote('code');
            var title = $('#modal_text_title').val();
            if (!content.trim() || content === '<p><br></p>') {
                alert('Please enter some text.');
                return;
            }
            $.ajax({
                url: "<?php echo base_url('text/save'); ?>",
                method: "POST",
                data: { 
                    text_content_base64: btoa(unescape(encodeURIComponent(content))).split('').reverse().join(''), 
                    text_title: title 
                },
                success: function (response) {
                    if (response.status == 1) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Initialize Summernote editor for main page
        if ($('#please_type_here').length) {
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
        }

        // Initialize DataTable for texts
        if ($('#texts-all-datatable').length) {
            $('#texts-all-datatable').DataTable({
                pageLength: 15,
                order: [[1, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter texts:' }
            });
        }

        // Save text functionality
        $('#save_text_btn').on('click', function () {
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
                    text_content_base64: btoa(unescape(encodeURIComponent(textContent))).split('').reverse().join(''),
                    text_title: textTitle
                },
                success: function (response) {
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
                error: function () {
                    alert('Error saving text. Please try again.');
                },
                complete: function () {
                    // Reset button state
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Clear text functionality
        $('#paste_text_btn').on('click', function() {
            var $btn = $(this);
            var originalHtml = $btn.html();

            function saveText(text) {
                if (!text || text.trim() === '') {
                    alert('Text is empty.');
                    return;
                }
                
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');
                $('#please_type_here').summernote('code', text);
                
                var b64Content = btoa(unescape(encodeURIComponent(text)));
                var reversedB64 = b64Content.split('').reverse().join('');
                
                $.ajax({
                    url: '<?php echo base_url("text/save"); ?>',
                    type: 'POST',
                    data: {
                        text_content_base64: reversedB64,
                        text_title: 'Pasted from Clipboard'
                    },
                    success: function (response) {
                        if (response.status == 1) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function() {
                        alert('Failed to save to server.');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }

            if (navigator.clipboard) {
                navigator.clipboard.readText().then(function(text) {
                    saveText(text);
                }).catch(function(err) {
                    var manualText = prompt('Clipboard access denied initially. Please paste (Ctrl+V) your text here explicitly:');
                    if (manualText !== null) {
                        saveText(manualText);
                    }
                });
            } else {
                var manualText = prompt('Please paste (Ctrl+V) your text here:');
                if (manualText !== null) {
                    saveText(manualText);
                }
            }
        });

        $('#clear_text_btn').on('click', function () {
            if (confirm('Are you sure you want to clear the text?')) {
                $('#please_type_here').summernote('code', '');
                $('#text_title').val('');
            }
        });

        // Copy to clipboard functionality
        $(document).on('click', '.copy-text-btn', function () {
            var textToCopy = $(this).data('text');

            // Use the Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(function () {
                    showCopySuccess();
                }).catch(function () {
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
            setTimeout(function () {
                $msg.alert('close');
            }, 3000);
        }

        // Function to refresh text lists
        function refreshTextLists() {
            $.ajax({
                url: '<?php echo base_url("home/texts"); ?>',
                type: 'GET',
                success: function (response) {
                    // This is a simple refresh - in a real implementation,
                    // you'd want to extract just the list parts from the response
                    location.reload();
                }
            });
        }


        // Share QR Functionality
        $(document).on('click', '.share-qr-btn', function () {
            var $this = $(this);
            var url = $this.data('url');
            var title = $this.data('title');
            var size = $this.data('size') || '';
            var date = $this.data('date') || '';

            $('#qr-modal-title').text(title);
            $('#qr-modal-size').text(size);
            $('#qr-modal-date').text(date);
            $('#qr-modal-container').empty();

            var qrModalElement = document.getElementById('share-qr-modal');
            if (qrModalElement) {
                var qrModal = bootstrap.Modal.getOrCreateInstance(qrModalElement);
                qrModal.show();

                setTimeout(function () {
                    new QRCode(document.getElementById('qr-modal-container'), {
                        text: url,
                        width: 256,
                        height: 256,
                        colorDark: "#313a46",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }, 350);
            }
        });
        // Global Upload Modal Dropzone
        if ($('#global-file-dropzone').length) {
            var globalDropzone = new Dropzone("#global-file-dropzone", {
                url: "<?php echo base_url('home/file/upload'); ?>",
                maxFilesize: 50,
                acceptedFiles: null,
                init: function () {
                    this.on("sending", function () { $('#global-upload-progress').show(); });
                    this.on("totaluploadprogress", function (progress) { $('#global-overall-bar').css('width', progress + '%'); });
                    this.on("queuecomplete", function () { setTimeout(function () { location.reload(); }, 1500); });
                }
            });
        }
    });
</script>

</body>

</html>