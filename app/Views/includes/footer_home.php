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

<!-- Dropzone -->
<link href="<?php echo base_url('assets/dropzone/dropzone.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/dropzone/dropzone.js')?>"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Summernote -->
<link href="<?php echo base_url('assets/summernote/summernote-bs4.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/summernote/summernote.min.js')?>"></script>


<?php include 'upload_modal.php'; ?>

<script>
    $(document).ready(function(){
        // QR Code Generation for Pairing (Main Sidebar)
        if (document.getElementById('pair-qr')) {
            setTimeout(function() {
                var container = document.getElementById('pair-qr');
                if (container) {
                    container.innerHTML = '';
                    new QRCode(container, {
                        text: window.location.origin,
                        width: 150,
                        height: 150,
                        colorDark : "#313a46",
                        colorLight : "#f1f3fa",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                }
            }, 800);
        }

        // Global Upload Modal Dropzone
        if ($('#global-file-dropzone').length) {
            var globalDropzone = new Dropzone("#global-file-dropzone", {
                url: "<?php echo base_url('home/file/upload'); ?>",
                maxFilesize: 50,
                init: function() {
                    this.on("sending", function() { $('#global-upload-progress').show(); });
                    this.on("totaluploadprogress", function(progress) { $('#global-overall-bar').css('width', progress + '%'); });
                    this.on("queuecomplete", function() { setTimeout(function() { location.reload(); }, 1500); });
                }
            });
        }

        // Global Modal Summernote
        if ($('#modal_summernote').length) {
            $('#modal_summernote').summernote({ height: 200 });
        }

        // Save text from modal
        $('#modal_save_text_btn').on('click', function() {
            var content = $('#modal_summernote').val();
            var title = $('#modal_text_title').val();
            if (!content.trim()) return;
            $.ajax({
                url: "<?php echo base_url('text/save'); ?>",
                method: "POST",
                data: { text_content: content, text_title: title },
                success: function(response) { if (response.status == 1) location.reload(); }
            });
        });

        // DataTable: Files
        if ($('#files-datatable').length) {
            $('#files-datatable').DataTable({
                pageLength: 15,
                order: [[3, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter:' }
            });
        }

        // DataTable: Texts
        if ($('#texts-datatable').length) {
            $('#texts-datatable').DataTable({
                pageLength: 15,
                order: [[2, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter:' }
            });
        }

        // Copy text functionality
        $(document).on('click', '.copy-text-link', function () {
            var textToCopy = $(this).data('text');
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(function () {
                    alert('Copied to clipboard!');
                });
            } else {
                var textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Copied to clipboard!');
            }
        });

        // Share QR
        $(document).on('click', '.share-qr-btn', function () {
            var url = $(this).data('url');
            var title = $(this).data('title');
            $('#qr-modal-title').text(title);
            $('#qr-modal-container').empty();
            $('#share-qr-modal').modal('show');
            setTimeout(function () {
                new QRCode(document.getElementById('qr-modal-container'), {
                    text: url, width: 256, height: 256
                });
            }, 300);
        });
    });
</script>

<!-- Share QR Modal -->
<div id="share-qr-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title font-18 text-white" id="qr-modal-title">Share QR</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
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