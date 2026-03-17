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

<script>
    $(document).ready(function() {
        // Restore file function
        window.restoreFile = function(fileUuid) {
            if (confirm('Are you sure you want to restore this file?')) {
                $.ajax({
                    url: '<?php echo base_url("api/restore-file"); ?>',
                    type: 'POST',
                    data: { file_uuid: fileUuid },
                    success: function(response) {
                        if (response.success) {
                            showNotification('File restored successfully!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to restore file: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to restore file. Please try again.', 'error');
                    }
                });
            }
        };

        // Restore text function
        window.restoreText = function(textUuid) {
            if (confirm('Are you sure you want to restore this text?')) {
                $.ajax({
                    url: '<?php echo base_url("api/restore-text"); ?>',
                    type: 'POST',
                    data: { text_uuid: textUuid },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Text restored successfully!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to restore text: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to restore text. Please try again.', 'error');
                    }
                });
            }
        };

        // Permanent delete file function
        window.permanentDeleteFile = function(fileUuid) {
            if (confirm('Are you sure you want to permanently delete this file? This action cannot be undone!')) {
                $.ajax({
                    url: '<?php echo base_url("api/permanent-delete-file"); ?>',
                    type: 'POST',
                    data: { file_uuid: fileUuid },
                    success: function(response) {
                        if (response.success) {
                            showNotification('File permanently deleted!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to delete file: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to delete file. Please try again.', 'error');
                    }
                });
            }
        };

        // Permanent delete text function
        window.permanentDeleteText = function(textUuid) {
            if (confirm('Are you sure you want to permanently delete this text? This action cannot be undone!')) {
                $.ajax({
                    url: '<?php echo base_url("api/permanent-delete-text"); ?>',
                    type: 'POST',
                    data: { text_uuid: textUuid },
                    success: function(response) {
                        if (response.success) {
                            showNotification('Text permanently deleted!', 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to delete text: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to delete text. Please try again.', 'error');
                    }
                });
            }
        };

        // Empty trash function
        window.emptyTrash = function() {
            const totalItems = <?php echo $total_deleted; ?>;
            if (totalItems === 0) {
                showNotification('Trash is already empty.', 'info');
                return;
            }

            if (confirm(`Are you sure you want to permanently delete all ${totalItems} items in trash? This action cannot be undone!`)) {
                $.ajax({
                    url: '<?php echo base_url("api/empty-trash"); ?>',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification(`Successfully deleted ${response.deleted_count} items from trash!`, 'success');
                            location.reload();
                        } else {
                            showNotification('Failed to empty trash: ' + (response.message || 'Unknown error'), 'error');
                        }
                    },
                    error: function() {
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

            setTimeout(function() {
                notification.alert('close');
            }, 5000);
        }

        // Add some visual enhancements
        $('.file-row').hover(
            function() { $(this).addClass('table-active'); },
            function() { $(this).removeClass('table-active'); }
        );

        $('.text-row').hover(
            function() { $(this).addClass('table-active'); },
            function() { $(this).removeClass('table-active'); }
        );

        // QR Code Generation for Pairing
        if (document.getElementById('pair-qr')) {
            new QRCode(document.getElementById('pair-qr'), {
                text: window.location.origin + "/home/trashed",
                width: 100,
                height: 100,
                colorDark : "#313a46",
                colorLight : "#f1f3fa",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
    });
</script>
</body>
</html>
