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

<?php include 'upload_modal.php'; ?>
<?php include 'qr_modal.php'; ?>

<script>
    $(document).ready(function () {
        // QR Code Generation for Pairing (Main Sidebar)
        if (document.getElementById('pair-qr')) {
            setTimeout(function () {
                var container = document.getElementById('pair-qr');
                if (container) {
                    container.innerHTML = '';
                    new QRCode(container, {
                        text: window.location.origin,
                        width: 150,
                        height: 150,
                        colorDark: "#313a46",
                        colorLight: "#f1f3fa",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
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

        // Restore file function
        window.restoreFile = function (fileUuid) {
            if (confirm('Are you sure you want to restore this file?')) {
                $.ajax({
                    url: '<?php echo base_url("api/restore-file"); ?>',
                    type: 'POST',
                    data: { file_uuid: fileUuid },
                    success: function (response) {
                        if (response.success) {
                            showNotification('File restored successfully!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to restore file: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function () {
                        showNotification('Failed to restore file. Please try again.', 'error');
                    }
                });
            }
        };

        // Restore text function
        window.restoreText = function (textUuid) {
            if (confirm('Are you sure you want to restore this text?')) {
                $.ajax({
                    url: '<?php echo base_url("api/restore-text"); ?>',
                    type: 'POST',
                    data: { text_uuid: textUuid },
                    success: function (response) {
                        if (response.success) {
                            showNotification('Text restored successfully!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to restore text: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function () {
                        showNotification('Failed to restore text. Please try again.', 'error');
                    }
                });
            }
        };

        // Permanent delete file function
        window.permanentDeleteFile = function (fileUuid) {
            if (confirm('Are you sure you want to permanently delete this file? This action cannot be undone!')) {
                $.ajax({
                    url: '<?php echo base_url("api/permanent-delete-file"); ?>',
                    type: 'POST',
                    data: { file_uuid: fileUuid },
                    success: function (response) {
                        if (response.success) {
                            showNotification('File permanently deleted!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to delete file: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function () {
                        showNotification('Failed to delete file. Please try again.', 'error');
                    }
                });
            }
        };

        // Permanent delete text function
        window.permanentDeleteText = function (textUuid) {
            if (confirm('Are you sure you want to permanently delete this text? This action cannot be undone!')) {
                $.ajax({
                    url: '<?php echo base_url("api/permanent-delete-text"); ?>',
                    type: 'POST',
                    data: { text_uuid: textUuid },
                    success: function (response) {
                        if (response.success) {
                            showNotification('Text permanently deleted!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to delete text: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function () {
                        showNotification('Failed to delete text. Please try again.', 'error');
                    }
                });
            }
        };

        // Empty trash function
        window.emptyTrash = function () {
            const totalItems = <? php echo $total_deleted; ?>;
            if (totalItems === 0) {
                showNotification('Trash is already empty.', 'info');
                return;
            }

            if (confirm(`Are you sure you want to permanently delete all ${totalItems} items in trash? This action cannot be undone!`)) {
                $.ajax({
                    url: '<?php echo base_url("api/empty-trash"); ?>',
                    type: 'POST',
                    success: function (response) {
                        if (response.success) {
                            showNotification(`Successfully deleted ${response.deleted_count} items from trash!`, 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to empty trash: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function () {
                        showNotification('Failed to empty trash. Please try again.', 'error');
                    }
                });
            }
        };

        // Show notification function
        function showNotification(message, type) {
            var alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' :
                    type === 'warning' ? 'alert-warning' : 'alert-info';
            var icon = type === 'success' ? 'check-circle' :
                type === 'error' ? 'alert-circle' :
                    type === 'warning' ? 'alert' : 'information';

            var notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="mdi mdi-${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

            $('body').append(notification);

            setTimeout(function () {
                notification.alert('close');
            }, 5000);
        }

        // Add some visual enhancements
        $('.file-row').hover(
            function () { $(this).addClass('table-active'); },
            function () { $(this).removeClass('table-active'); }
        );

        $('.text-row').hover(
            function () { $(this).addClass('table-active'); },
            function () { $(this).removeClass('table-active'); }
        );

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