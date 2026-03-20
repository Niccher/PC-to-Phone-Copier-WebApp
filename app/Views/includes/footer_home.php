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

<!-- Dropzone -->
<link href="<?php echo base_url('assets/dropzone/dropzone.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/dropzone/dropzone.js')?>"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Summernote Lite -->
<link href="<?php echo base_url('assets/summernote/summernote-lite.min.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/summernote/summernote-lite.min.js')?>"></script>


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
                order: [[3, 'desc']],
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
                        text: url, width: 256, height: 256,
                        colorDark: "#313a46",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }, 350);
            }
        });
        // Global Upload Modal Dropzone
        if ($('#global-file-dropzone').length) {
            Dropzone.autoDiscover = false;
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