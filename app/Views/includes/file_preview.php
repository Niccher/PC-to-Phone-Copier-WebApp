<!-- File Preview Modal -->
<div id="file-preview-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h4 class="modal-title font-18 text-white" id="file-preview-title">File Preview</h4>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="preview-download-btn">
                        <i class="mdi mdi-download me-1"></i>Download
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left Side: Preview Area -->
                    <div class="col-lg-8 bg-light d-flex align-items-center justify-content-center p-4"
                        style="min-height: 400px; border-right: 1px solid #eee;">
                        <div id="file-preview-content" class="w-100 text-center">
                            <!-- Content injected by JS -->
                        </div>
                    </div>
                    <!-- Right Side: Details & Actions -->
                    <div class="col-lg-4 p-4">
                        <h5 class="mb-3 d-flex align-items-center">
                            <i class="mdi mdi-information-outline me-2 text-primary"></i>File Details
                        </h5>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted small fw-bold text-uppercase">Name</span>
                                <span class="text-dark small text-truncate ms-3 fw-semibold" id="file-detail-name">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted small fw-bold text-uppercase">Size</span>
                                <span class="text-dark small fw-semibold" id="file-detail-size">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted small fw-bold text-uppercase">Category</span>
                                <span class="text-dark small fw-semibold" id="file-detail-type">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted small fw-bold text-uppercase">Uploaded</span>
                                <span class="text-dark small fw-semibold" id="file-detail-date">-</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Tags</label>
                            <div id="file-tags-container" class="mb-2">
                                <!-- Tags injected by JS -->
                            </div>
                            <div class="input-group input-group-sm">
                                <input type="text" id="add-tag-input" class="form-control" placeholder="Add a tag...">
                                <button class="btn btn-primary" type="button" id="add-tag-btn">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
                            <textarea id="file-description" class="form-control form-control-sm" rows="3"
                                placeholder="Add a description..."></textarea>
                            <div class="text-end mt-1">
                                <button type="button" class="btn btn-xs btn-link p-0 text-primary" id="save-description-btn">
                                    <i class="mdi mdi-content-save-outline me-1"></i>Save
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-outline-info shadow-sm share-qr-btn-modal">
                                <i class="mdi mdi-qrcode me-2"></i>Share via QR
                            </button>
                            <button type="button" class="btn btn-outline-danger shadow-sm" id="preview-delete-btn">
                                <i class="mdi mdi-delete-outline me-2"></i>Delete File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure availableTags exists globally if not already defined
    if (typeof availableTags === 'undefined') {
        var availableTags = [];
    }

    function showFilePreview(fileUuid) {
        // Show loading state in modal
        $('#file-preview-title').text('Loading Preview...');
        $('#file-preview-content').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
        $('#file-detail-name, #file-detail-size, #file-detail-type, #file-detail-date').text('-');
        $('#file-tags-container').empty();
        $('#file-description').val('');

        var previewModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('file-preview-modal'));
        previewModal.show();

        // Save UUID to modal for actions
        $('#file-preview-modal').data('file-uuid', fileUuid);

        // Fetch file details via AJAX
        $.ajax({
            url: '<?php echo base_url("files/preview/"); ?>' + fileUuid,
            type: 'GET',
            success: function (response) {
                if (response.status == 1) {
                    var file = response.file;
                    $('#file-preview-title').text(file.up_file_Orig_Name);
                    $('#file-detail-name').text(file.up_file_Orig_Name).attr('title', file.up_file_Orig_Name);
                    $('#file-detail-size').text(formatFileSize(file.up_file_Size));
                    $('#file-detail-type').text(file.up_file_category || 'Other');
                    $('#file-detail-date').text(new Date(file.up_file_Created_at).toLocaleString());
                    $('#file-description').val(file.up_file_description || '');

                    // Render tags
                    $('#file-tags-container').html(generateTagBadges(file.up_file_tags));

                    // Render preview content based on extension
                    var ext = file.up_file_Extension.toLowerCase();
                    var previewContent = '';
                    var viewUrl = '<?php echo base_url("saved/view/"); ?>' + fileUuid;

                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                        previewContent = `<img src="${viewUrl}" class="img-fluid rounded shadow-sm" style="max-height: 70vh;">`;
                    } else if (ext === 'pdf') {
                        previewContent = `<iframe src="${viewUrl}" width="100%" height="600px" style="border: none;"></iframe>`;
                    } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                        previewContent = `<video controls class="w-100 rounded shadow-sm" style="max-height: 70vh;">
                                        <source src="${viewUrl}" type="video/${ext === 'mp4' ? 'mp4' : ext}">
                                        Your browser does not support the video tag.
                                      </video>`;
                    } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                        previewContent = `<div class="p-5"><audio controls class="w-100">
                                        <source src="${viewUrl}" type="audio/${ext === 'mp3' ? 'mpeg' : ext}">
                                        Your browser does not support the audio tag.
                                      </audio></div>`;
                    } else if (['txt', 'php', 'js', 'css', 'sql', 'json', 'html'].includes(ext)) {
                        // For text files, we fetch the content
                        $.get(viewUrl, function (data) {
                            $('#file-preview-content').html(`<pre class="text-start bg-dark text-light p-3 rounded" style="max-height: 60vh; overflow-y: auto;"><code>${escapeHtml(data)}</code></pre>`);
                        });
                        return; // Exit early as we'll update content asynchronously
                    } else {
                        previewContent = `
                        <div class="py-5">
                            <i class="mdi mdi-file-question display-1 text-muted"></i>
                            <p class="mt-3">Preview not available for this file type.</p>
                            <a href="<?php echo base_url('saved/download/'); ?>${fileUuid}" class="btn btn-primary mt-2">
                                <i class="mdi mdi-download me-1"></i>Download instead
                            </a>
                        </div>`;
                    }
                    $('#file-preview-content').html(previewContent);
                } else {
                    $('#file-preview-content').html('<div class="alert alert-danger">Error loading file details.</div>');
                }
            },
            error: function () {
                $('#file-preview-content').html('<div class="alert alert-danger">Failed to fetch file details.</div>');
            }
        });
    }

    // Shared Event Listeners
    $(document).ready(function () {
        // File preview modal trigger (Delegated)
        $(document).on('click', '.file-preview-btn', function () {
            var fileUuid = $(this).data('file-uuid');
            showFilePreview(fileUuid);
        });

        // Modal Specific Actions
        $('#preview-download-btn').on('click', function () {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            window.location.href = '<?php echo base_url("saved/download/"); ?>' + fileUuid;
        });

        $('#preview-delete-btn').on('click', function () {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            if(confirm('Are you sure you want to delete this file?')) {
                // If special delete functions exist in the page (like in trash or files), use them
                if (typeof deleteFile === 'function') {
                    deleteFile(fileUuid);
                } else if (typeof permanentDeleteFile === 'function') {
                    permanentDeleteFile(fileUuid);
                } else {
                    // Fallback to simple delete redirect if no AJAX function
                    window.location.href = '<?php echo base_url("saved/delete/"); ?>' + fileUuid;
                }
                $('#file-preview-modal').modal('hide');
            }
        });

        $('#save-description-btn').on('click', function () {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var description = $('#file-description').val();
            if (typeof updateFileDescription === 'function') {
                updateFileDescription(fileUuid, description);
            } else {
                // Inline update if function not available
                $.post('<?php echo base_url("files/update-description"); ?>', { file_uuid: fileUuid, description: description });
            }
        });

        $('#add-tag-btn').on('click', function () {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var tagName = $('#add-tag-input').val().trim();
            if (tagName) {
                if (typeof addFileTag === 'function') {
                    addFileTag(fileUuid, tagName);
                    $('#add-tag-input').val('');
                } else {
                    $.post('<?php echo base_url("files/add-tag"); ?>', { file_uuid: fileUuid, tag_name: tagName }, function() {
                        $('#file-preview-modal').modal('hide');
                        location.reload();
                    });
                }
            }
        });

        $('#add-tag-input').on('keypress', function (e) {
            if (e.which == 13) $('#add-tag-btn').click();
        });

        $(document).on('click', '.share-qr-btn-modal', function () {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            // Try to find an existing share button and trigger it, or just use the logic
            $(`.share-qr-btn[data-url*="${fileUuid}"]`).first().click();
        });
    });

    // Helper functions (if not already defined)
    if (typeof formatFileSize !== 'function') {
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'], i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }

    if (typeof generateTagBadges !== 'function') {
        function generateTagBadges(tags) {
            if (!tags) return '';
            var tagArray = tags.split(',');
            var html = '';
            tagArray.forEach(function (tag) {
                var color = '#6c757d';
                if (typeof availableTags !== 'undefined') {
                    var tagInfo = availableTags.find(t => t.tag_name === tag.trim());
                    if (tagInfo) color = tagInfo.tag_color;
                }
                html += `<span class="badge me-1" style="background-color: ${color};">${tag.trim()}</span>`;
            });
            return html;
        }
    }

    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    }

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

        setTimeout(function () {
            notification.alert('close');
        }, 5000);
    }
</script>
