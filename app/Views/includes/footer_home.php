
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
    $(document).ready(function(){
        // Search functionality for recent items
        $('#recent-search').on('input', function() {
            var searchTerm = $(this).val().toLowerCase().trim();

            if (searchTerm === '') {
                // Show all items
                $('.file-card, .text-card').show();
                $('.tab-pane .col-12 .text-center').hide();
            } else {
                // Filter items
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                var visibleItems = 0;

                $(activeTab + ' .file-card, ' + activeTab + ' .text-card').each(function() {
                    var itemText = $(this).text().toLowerCase();
                    if (itemText.includes(searchTerm)) {
                        $(this).show();
                        visibleItems++;
                    } else {
                        $(this).hide();
                    }
                });

                // Show/hide "no results" message
                var noResultsMsg = $(activeTab + ' .col-12 .text-center');
                if (visibleItems === 0) {
                    if (noResultsMsg.length === 0) {
                        $(activeTab).prepend(`
                                    <div class="col-12">
                                        <div class="text-center py-4">
                                            <i class="mdi mdi-magnify display-4 text-muted mb-2"></i>
                                            <h6 class="text-muted">No items match your search</h6>
                                            <p class="text-muted">Try different keywords</p>
                                        </div>
                                    </div>
                                `);
                    } else {
                        noResultsMsg.show();
                    }
                } else {
                    noResultsMsg.hide();
                }
            }
        });

        // Clear search when switching tabs
        $('.nav-tabs .nav-link').on('shown.bs.tab', function() {
            $('#recent-search').val('');
            $('.file-card, .text-card').show();
            $('.tab-pane .col-12 .text-center').hide();
        });

        // Copy text functionality
        $(document).on('click', '.copy-text-link', function() {
            var textToCopy = $(this).data('text');

            // Use the Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    showNotification('Text copied to clipboard!', 'success');
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
                    showNotification('Text copied to clipboard!', 'success');
                } else {
                    showNotification('Failed to copy text', 'error');
                }
            } catch (err) {
                showNotification('Failed to copy text', 'error');
            }

            document.body.removeChild(textArea);
        }

        // Show notification
        function showNotification(message, type) {
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'check-circle' : 'alert-circle';

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
            }, 3000);
        }

        // Refresh recent items
        window.refreshRecent = function() {
            location.reload();
        };

        // Auto-refresh every 30 seconds (less frequent than before)
        setInterval(function() {
            // Only refresh if search is empty
            if ($('#recent-search').val().trim() === '') {
                refreshRecent();
            }
        }, 30000);
    });
</script>
</body>
</html>
