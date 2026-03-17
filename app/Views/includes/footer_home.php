
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

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function(){

        // Init DataTable: Files
        if ($('#files-datatable').length) {
            $('#files-datatable').DataTable({
                pageLength: 15,
                order: [[4, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter files:' }
            });
        }

        // Init DataTable: Texts
        if ($('#texts-datatable').length) {
            $('#texts-datatable').DataTable({
                pageLength: 15,
                order: [[2, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter texts:' }
            });
        }

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

        // QR Code Generation for Pairing
        if (document.getElementById('pair-qr')) {
            new QRCode(document.getElementById('pair-qr'), {
                text: window.location.origin + "/home/recent",
                width: 100,
                height: 100,
                colorDark : "#313a46",
                colorLight : "#f1f3fa",
                correctLevel : QRCode.CorrectLevel.H
            });
        }

        // Share QR Functionalitiy
        $(document).on('click', '.share-qr-btn', function() {
            var url = $(this).data('url');
            var title = $(this).data('title');
            var size = $(this).data('size') || '';
            var date = $(this).data('date') || '';
            
            $('#qr-modal-title').text(title);
            $('#qr-modal-size').text(size);
            $('#qr-modal-date').text(date);
            $('#qr-modal-container').empty();
            
            $('#share-qr-modal').modal('show');
            
            setTimeout(function() {
                new QRCode(document.getElementById('qr-modal-container'), {
                    text: url,
                    width: 256,
                    height: 256,
                    colorDark : "#313a46",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            }, 300);
        });

        // Auto-refresh every 30 seconds
        setInterval(function() {
            if ($('#recent-search').val().trim() === '') {
                // refreshRecent(); // Disabled for testing UI
            }
        }, 30000);
    });
</script>

<!-- Share QR Modal -->
<div id="share-qr-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title font-18 text-white" id="qr-modal-title">Share QR</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="qr-modal-container" class="d-inline-block p-3 bg-white rounded shadow-sm mb-3"></div>
                <div class="mt-3">
                    <h5 class="mb-1 text-dark" id="qr-modal-item-title">Access QR Code</h5>
                    <p class="text-muted mb-3">Scan this code to access the item on another device.</p>
                    
                    <div class="d-flex justify-content-center gap-3 py-2 border-top border-bottom">
                        <div class="text-center">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold">Size</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-size">-</span>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold">Date</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-date">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
