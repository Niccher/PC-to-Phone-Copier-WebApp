
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

<!-- Dropzone js -->
<script src="<?php echo base_url('assets/dropzone/dropzone.js')?>"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- File Management JavaScript -->
<script>
    // Global variables
    var currentFiles = [];
    var selectedFiles = [];
    var currentView = 'list';
    var categories = [];
    var availableTags = [];

    $(document).ready(function() {
        // Initialize DataTable for file manager
        if ($('#files-manager-table').length) {
            $('#files-manager-table').DataTable({
                pageLength: 20,
                order: [[5, 'desc']],
                columnDefs: [{ orderable: false, targets: 'no-sort' }],
                language: { search: 'Filter files:' }
            });
        }

        // Initialize components (for upload zone only on this page)
        initializeDropzone();
        loadCategoriesAndTags();

        // QR Code Generation for Pairing
        if (document.getElementById('pair-qr')) {
            new QRCode(document.getElementById('pair-qr'), {
                text: window.location.origin + "/home/files",
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

        // Set up event listeners
        setupEventListeners();

        console.log('File manager initialized');
    });

    function initializeDropzone() {
        // Configure Dropzone
        Dropzone.autoDiscover = false;

        var uploadQueue = [];
        var completedUploads = 0;
        var totalUploads = 0;

        var myDropzone = new Dropzone("#file-upload-dropzone", {
            url: "<?php echo base_url('home/file/upload'); ?>",
            method: "post",
            paramName: "file",
            maxFilesize: 50,
            maxFiles: 10,
            parallelUploads: 2,
            uploadMultiple: false,
            acceptedFiles: ".pdf,.doc,.docx,.txt,.rtf,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.tiff,.webp,.zip,.rar,.7z,.tar,.gz,.mp3,.wav,.flac,.aac,.ogg,.mp4,.avi,.mov,.wmv,.flv,.webm,.html,.css,.js,.php,.py,.java,.cpp,.c,.h,.xml,.json,.yaml,.yml",
            dictDefaultMessage: "",
            dictFallbackMessage: "Your browser does not support drag and drop file uploads.",
            dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
            dictInvalidFileType: "You can't upload files of this type.",
            dictResponseError: "Server responded with {{statusCode}} code.",
            dictMaxFilesExceeded: "You can not upload any more files.",
            dictCancelUpload: "Cancel upload",
            dictUploadCanceled: "Upload canceled",
            dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",

            init: function() {
                var dz = this;

                this.on("addedfile", function(file) {
                    uploadQueue.push(file);
                    updateQueueCount();
                });

                this.on("removedfile", function(file) {
                    var index = uploadQueue.indexOf(file);
                    if (index > -1) {
                        uploadQueue.splice(index, 1);
                    }
                    updateQueueCount();
                });

                this.on("totaluploadprogress", function(progress, totalBytes, totalBytesSent) {
                    updateOverallProgress(progress);
                });

                this.on("uploadprogress", function(file, progress, bytesSent) {
                    updateFileProgress(file, progress);
                });

                this.on("success", function(file, response) {
                    handleUploadSuccess(file, response);
                });

                this.on("error", function(file, message, xhr) {
                    handleUploadError(file, message, xhr);
                });

                this.on("queuecomplete", function() {
                    handleQueueComplete();
                });

                this.on("addedfile", function() {
                    $("#upload-progress-container").show();
                });
            },

            accept: function(file, done) {
                var allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx',
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp',
                    'zip', 'rar', '7z', 'tar', 'gz',
                    'mp3', 'wav', 'flac', 'aac', 'ogg',
                    'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm',
                    'html', 'css', 'js', 'php', 'py', 'java', 'cpp', 'c', 'h',
                    'xml', 'json', 'yaml', 'yml'];

                var fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    done("File type not allowed. Please check the supported formats list.");
                    return;
                }

                if (file.size > 50 * 1024 * 1024) {
                    done("File size too large. Maximum allowed size is 50MB.");
                    return;
                }

                done();
            }
        });

        function updateQueueCount() {
            var count = uploadQueue.length;
            $("#upload-queue-count").text(count + " in queue");

            if (count === 0) {
                $("#upload-progress-container").hide();
            }
        }

        function updateOverallProgress(progress) {
            $("#overall-progress-bar").css("width", progress + "%");
            $("#overall-progress-text").text(Math.round(progress) + "%");
        }

        function updateFileProgress(file, progress) {
            var fileElement = $(file.previewElement);
            var progressBar = fileElement.find(".dz-upload .dz-upload-progress");

            if (progressBar.length === 0) {
                fileElement.find(".dz-details").append(`
                            <div class="dz-upload">
                                <div class="dz-upload-progress" style="width: ${progress}%"></div>
                            </div>
                        `);
            } else {
                progressBar.css("width", progress + "%");
            }
        }

        function handleUploadSuccess(file, response) {
            completedUploads++;

            if (response.status == 1) {
                showNotification("File uploaded successfully: " + file.name, "success");
                setTimeout(function() {
                    myDropzone.removeFile(file);
                    loadFiles(); // Refresh file list
                }, 2000);
            } else {
                handleUploadError(file, response.message, null, response.error_type);
            }
        }

        function handleUploadError(file, message, xhr, errorType) {
            $(file.previewElement).addClass("dz-error");

            var errorMessage = message || "Upload failed";
            var errorElement = $(file.previewElement).find(".dz-error-message");

            if (errorElement.length === 0) {
                $(file.previewElement).append('<div class="dz-error-message"><span>' + errorMessage + '</span></div>');
            } else {
                errorElement.html('<span>' + errorMessage + '</span>');
            }

            showNotification("Upload failed: " + file.name + " - " + errorMessage, "error");

            setTimeout(function() {
                myDropzone.removeFile(file);
            }, 5000);
        }

        function handleQueueComplete() {
            if (completedUploads === totalUploads && totalUploads > 0) {
                showNotification("All files uploaded successfully!", "success");
                completedUploads = 0;
                totalUploads = 0;

                setTimeout(function() {
                    $("#upload-progress-container").hide();
                }, 3000);
            }
        }
    }

    function setupEventListeners() {
        // Modal search functionality
        $('#apply-search-btn').on('click', function() {
            var searchParams = {
                search: $('#modal-file-search').val(),
                category: $('#modal-category-filter').val(),
                file_type: $('#modal-type-filter').val()
            };
            loadFiles(searchParams);
            $('#search-modal').modal('hide');
        });

        $('#clear-search-btn').on('click', function() {
            $('#modal-file-search').val('');
            $('#modal-category-filter').val('');
            $('#modal-type-filter').val('');
            loadFiles(); // Load all files
            $('#search-modal').modal('hide');
        });

        // Enter key support in search modal
        $('#modal-file-search').on('keypress', function(e) {
            if (e.which === 13) {
                $('#apply-search-btn').click();
            }
        });

        // View toggle
        $('.view-toggle').on('click', function(e) {
            e.preventDefault();
            var view = $(this).data('view');
            setView(view);
        });

        // File selection
        $(document).on('change', '.file-checkbox', function() {
            updateSelectedFiles();
        });

        // Batch actions
        $('#batch-select-all').on('click', function() {
            var allChecked = $('.file-checkbox:checked').length === $('.file-checkbox').length;
            $('.file-checkbox').prop('checked', !allChecked);
            updateSelectedFiles();
        });

        $('#batch-delete').on('click', function() {
            if (selectedFiles.length === 0) return;

            if (confirm('Are you sure you want to delete ' + selectedFiles.length + ' selected files?')) {
                batchDeleteFiles(selectedFiles);
            }
        });

        $('#batch-add-tag').on('click', function() {
            if (selectedFiles.length === 0) return;

            var tagName = prompt('Enter tag name to add to selected files:');
            if (tagName && tagName.trim()) {
                batchAddTag(selectedFiles, tagName.trim());
            }
        });

        // File preview modal
        $(document).on('click', '.file-preview-btn', function() {
            var fileUuid = $(this).data('file-uuid');
            showFilePreview(fileUuid);
        });

        // File actions in preview modal
        $('#download-file-btn').on('click', function() {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            window.open('<?php echo base_url("saved/download/"); ?>' + fileUuid, '_blank');
        });

        $('#rename-file-btn').on('click', function() {
            var currentName = $('#file-detail-name').text();
            $('#rename-input').val(currentName);
            $('#rename-modal').modal('show');
        });

        $('#delete-file-btn').on('click', function() {
            if (confirm('Are you sure you want to delete this file?')) {
                var fileUuid = $('#file-preview-modal').data('file-uuid');
                deleteFile(fileUuid);
                $('#file-preview-modal').modal('hide');
            }
        });

        // Rename modal
        $('#confirm-rename-btn').on('click', function() {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var newName = $('#rename-input').val().trim();

            if (newName && newName !== $('#file-detail-name').text()) {
                renameFile(fileUuid, newName);
                $('#rename-modal').modal('hide');
            }
        });

        // Tag management
        $('#add-tag-btn').on('click', function() {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var tagName = $('#new-tag-input').val().trim();

            if (tagName) {
                addFileTag(fileUuid, tagName);
                $('#new-tag-input').val('');
            }
        });

        $(document).on('click', '.remove-tag-btn', function() {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var tagName = $(this).data('tag-name');
            removeFileTag(fileUuid, tagName);
        });

        // Description saving
        $('#save-description-btn').on('click', function() {
            var fileUuid = $('#file-preview-modal').data('file-uuid');
            var description = $('#file-description').val().trim();
            updateFileDescription(fileUuid, description);
        });
    }

    function loadCategoriesAndTags() {
        $.ajax({
            url: '<?php echo base_url("files/metadata"); ?>',
            type: 'GET',
            success: function(response) {
                if (response.status == 1) {
                    categories = response.categories;
                    availableTags = response.tags;

                    // Populate category filter
                    var categoryHtml = '<option value="">All Categories</option>';
                    categories.forEach(function(category) {
                        categoryHtml += '<option value="' + category.category_name + '">' + category.category_name + '</option>';
                    });
                    $('#category-filter').html(categoryHtml);
                }
            }
        });
    }

    function loadFiles(searchParams = {}) {
        // If no search parameters, load recent files
        var url = '<?php echo base_url("files/search"); ?>';
        var data = {};

        if (Object.keys(searchParams).length === 0) {
            // Load recent files by default
            data.limit = 50; // Load more files by default
        } else {
            // Apply search filters
            data = searchParams;
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function(response) {
                if (response.status == 1) {
                    currentFiles = response.files || [];
                    updateFileDisplay(currentFiles);
                }
            }
        });
    }

    function filterFiles(searchTerm, category, fileType) {
        var filteredFiles = currentFiles.filter(function(file) {
            var matchesSearch = !searchTerm ||
                file.up_file_Orig_Name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (file.up_file_tags && file.up_file_tags.toLowerCase().includes(searchTerm.toLowerCase())) ||
                (file.up_file_description && file.up_file_description.toLowerCase().includes(searchTerm.toLowerCase()));

            var matchesCategory = !category || file.up_file_category === category;
            var matchesType = !fileType || file.up_file_Extension === fileType;

            return matchesSearch && matchesCategory && matchesType;
        });

        updateFileDisplay(filteredFiles);
    }

    function updateFileDisplay(files) {
        $('#file-count').text(files.length + ' files');
        var container = $('#files-container');

        if (files.length === 0) {
            container.html(`
                        <div class="text-center py-5">
                            <i class="mdi mdi-folder-open display-4 text-muted"></i>
                            <h5 class="text-muted mt-2">No files found</h5>
                            <p class="text-muted">Upload some files or adjust your search criteria</p>
                        </div>
                    `);
            return;
        }

        if (currentView === 'grid') {
            container.html(generateGridView(files));
        } else {
            container.html(generateListView(files));
        }
    }

    function generateGridView(files) {
        var html = '<div class="row">';
        files.forEach(function(file) {
            var iconClass = getFileIcon(file.up_file_Extension);
            var thumbnail = file.up_file_thumbnail ? '<?php echo base_url(); ?>' + file.up_file_thumbnail : '';

            html += `
                        <div class="col-xxl-3 col-lg-4 col-md-6 mb-3">
                            <div class="card file-card h-100">
                                <div class="card-body text-center">
                                    <div class="file-checkbox-container">
                                        <input type="checkbox" class="form-check-input file-checkbox" value="${file.up_file_uuid}">
                                    </div>
                                    ${thumbnail ?
                `<img src="${thumbnail}" class="file-thumbnail mb-2" alt="${file.up_file_Orig_Name}">` :
                `<i class="mdi mdi-${iconClass} display-4 text-primary mb-2"></i>`
            }
                                    <h6 class="file-name text-truncate" title="${file.up_file_Orig_Name}">${file.up_file_Orig_Name}</h6>
                                    <p class="file-size text-muted small">${formatFileSize(file.up_file_Size)}</p>
                                    ${file.up_file_tags ? `<div class="file-tags mt-2">${generateTagBadges(file.up_file_tags)}</div>` : ''}
                                    <div class="file-actions mt-2">
                                        <button class="btn btn-sm btn-outline-primary file-preview-btn" data-file-uuid="${file.up_file_uuid}">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <a href="<?php echo base_url('saved/download/'); ?>${file.up_file_uuid}" class="btn btn-sm btn-outline-success">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-info share-qr-btn" 
                                            data-url="<?php echo base_url('saved/download/'); ?>${file.up_file_uuid}" 
                                            data-title="${file.up_file_Orig_Name}"
                                            data-size="${formatFileSize(file.up_file_Size)}"
                                            data-date="${new Date(file.up_file_Created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}">
                                            <i class="mdi mdi-qrcode"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFile('${file.up_file_uuid}')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
        });
        html += '</div>';
        return html;
    }

    function generateListView(files) {
        var html = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="select-all-files"></th>
                                    <th>Name</th>
                                    <th>Size</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Tags</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

        files.forEach(function(file) {
            html += `
                        <tr>
                            <td><input type="checkbox" class="form-check-input file-checkbox" value="${file.up_file_uuid}"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-${getFileIcon(file.up_file_Extension)} me-2"></i>
                                    <span class="file-name" title="${file.up_file_Orig_Name}">${file.up_file_Orig_Name}</span>
                                </div>
                            </td>
                            <td>${formatFileSize(file.up_file_Size)}</td>
                            <td>${file.up_file_Extension.toUpperCase()}</td>
                            <td>${file.up_file_category || 'Other'}</td>
                            <td>${file.up_file_tags ? generateTagBadges(file.up_file_tags) : '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary file-preview-btn" data-file-uuid="${file.up_file_uuid}">
                                    <i class="mdi mdi-eye"></i>
                                </button>
                                <a href="<?php echo base_url('saved/download/'); ?>${file.up_file_uuid}" class="btn btn-sm btn-outline-success">
                                    <i class="mdi mdi-download"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info share-qr-btn" 
                                    data-url="<?php echo base_url('saved/download/'); ?>${file.up_file_uuid}" 
                                    data-title="${file.up_file_Orig_Name}"
                                    data-size="${formatFileSize(file.up_file_Size)}"
                                    data-date="${new Date(file.up_file_Created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}">
                                    <i class="mdi mdi-qrcode"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteFile('${file.up_file_uuid}')">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </td>
                        </tr>
                    `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
                `;

        return html;
    }

    function generateTagBadges(tags) {
        if (!tags) return '';
        var tagArray = tags.split(',');
        var html = '';
        tagArray.forEach(function(tag) {
            var tagInfo = availableTags.find(t => t.tag_name === tag.trim());
            var color = tagInfo ? tagInfo.tag_color : '#6c757d';
            html += `<span class="badge me-1" style="background-color: ${color};">${tag.trim()}</span>`;
        });
        return html;
    }

    function getFileIcon(extension) {
        var iconMap = {
            'pdf': 'file-pdf',
            'doc': 'file-word',
            'docx': 'file-word',
            'xls': 'file-excel',
            'xlsx': 'file-excel',
            'ppt': 'file-powerpoint',
            'pptx': 'file-powerpoint',
            'txt': 'file-document',
            'jpg': 'file-image',
            'jpeg': 'file-image',
            'png': 'file-image',
            'gif': 'file-image',
            'mp4': 'file-video',
            'avi': 'file-video',
            'zip': 'folder-zip',
            'rar': 'folder-zip',
            'mp3': 'file-music',
            'wav': 'file-music'
        };
        return iconMap[extension.toLowerCase()] || 'file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function setView(view) {
        currentView = view;
        $('.view-toggle').removeClass('active');
        $('.view-toggle[data-view="' + view + '"]').addClass('active');
        updateFileDisplay(currentFiles);
    }

    function updateSelectedFiles() {
        selectedFiles = [];
        $('.file-checkbox:checked').each(function() {
            selectedFiles.push($(this).val());
        });

        if (selectedFiles.length > 0) {
            $('#batch-actions').show();
        } else {
            $('#batch-actions').hide();
        }
    }

    function showFilePreview(fileUuid) {
        $.ajax({
            url: '<?php echo base_url("files/details/"); ?>' + fileUuid,
            type: 'GET',
            success: function(response) {
                if (response.status == 1) {
                    var file = response.file;
                    $('#file-preview-modal').data('file-uuid', fileUuid);
                    $('#file-preview-title').text(file.up_file_Orig_Name);

                    // Set file details
                    $('#file-detail-name').text(file.up_file_Orig_Name);
                    $('#file-detail-size').text(formatFileSize(file.up_file_Size));
                    $('#file-detail-type').text(file.up_file_Type);
                    $('#file-detail-category').text(file.up_file_category || 'Other');
                    $('#file-detail-date').text(new Date(file.up_file_Created_at).toLocaleString());

                    // Set preview content
                    if (file.up_file_preview_available && file.up_file_thumbnail) {
                        $('#file-preview-content').html(`
                                    <img src="<?php echo base_url(); ?>${file.up_file_thumbnail}" class="img-fluid rounded" alt="${file.up_file_Orig_Name}">
                                `);
                    } else {
                        var iconClass = getFileIcon(file.up_file_Extension);
                        $('#file-preview-content').html(`
                                    <i class="mdi mdi-${iconClass} display-4 text-primary"></i>
                                    <p class="text-muted mt-2">Preview not available for this file type</p>
                                `);
                    }

                    // Set tags
                    if (file.up_file_tags) {
                        var tagHtml = '';
                        file.up_file_tags.split(',').forEach(function(tag) {
                            tagHtml += `
                                        <span class="badge bg-secondary me-1 mb-1">
                                            ${tag.trim()}
                                            <button class="btn btn-sm btn-close remove-tag-btn ms-1" data-tag-name="${tag.trim()}" style="font-size: 10px;"></button>
                                        </span>
                                    `;
                        });
                        $('#file-tags-container').html(tagHtml);
                    } else {
                        $('#file-tags-container').html('<small class="text-muted">No tags</small>');
                    }

                    // Set description
                    $('#file-description').val(file.up_file_description || '');

                    $('#file-preview-modal').modal('show');
                }
            }
        });
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

        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    function deleteFile(fileUuid) {
        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: '<?php echo base_url("saved/delete/"); ?>' + fileUuid,
                type: 'GET',
                success: function() {
                    showNotification('File deleted successfully', 'success');
                    loadFiles();
                },
                error: function() {
                    showNotification('Failed to delete file', 'error');
                }
            });
        }
    }

    function renameFile(fileUuid, newName) {
        $.ajax({
            url: '<?php echo base_url("files/rename"); ?>',
            type: 'POST',
            data: { file_uuid: fileUuid, new_name: newName },
            success: function(response) {
                if (response.status == 1) {
                    showNotification('File renamed successfully', 'success');
                    $('#file-preview-modal').modal('hide');
                    loadFiles();
                } else {
                    showNotification('Failed to rename file: ' + response.message, 'error');
                }
            }
        });
    }

    function addFileTag(fileUuid, tagName) {
        $.ajax({
            url: '<?php echo base_url("files/add-tag"); ?>',
            type: 'POST',
            data: { file_uuid: fileUuid, tag_name: tagName },
            success: function(response) {
                if (response.status == 1) {
                    showNotification('Tag added successfully', 'success');
                    showFilePreview(fileUuid); // Refresh preview
                } else {
                    showNotification('Failed to add tag', 'error');
                }
            }
        });
    }

    function removeFileTag(fileUuid, tagName) {
        $.ajax({
            url: '<?php echo base_url("files/remove-tag"); ?>',
            type: 'POST',
            data: { file_uuid: fileUuid, tag_name: tagName },
            success: function(response) {
                if (response.status == 1) {
                    showNotification('Tag removed successfully', 'success');
                    showFilePreview(fileUuid); // Refresh preview
                } else {
                    showNotification('Failed to remove tag', 'error');
                }
            }
        });
    }

    function updateFileDescription(fileUuid, description) {
        $.ajax({
            url: '<?php echo base_url("files/update-description"); ?>',
            type: 'POST',
            data: { file_uuid: fileUuid, description: description },
            success: function(response) {
                if (response.status == 1) {
                    showNotification('Description updated successfully', 'success');
                } else {
                    showNotification('Failed to update description', 'error');
                }
            }
        });
    }

    function batchDeleteFiles(fileUuids) {
        $.ajax({
            url: '<?php echo base_url("files/batch-delete"); ?>',
            type: 'POST',
            data: { file_uuids: fileUuids },
            success: function(response) {
                if (response.status == 1) {
                    showNotification(`${response.deleted_count} files deleted successfully`, 'success');
                    loadFiles();
                } else {
                    showNotification('Failed to delete some files', 'error');
                }
            }
        });
    }

    function batchAddTag(fileUuids, tagName) {
        $.ajax({
            url: '<?php echo base_url("files/batch-add-tag"); ?>',
            type: 'POST',
            data: { file_uuids: fileUuids, tag_name: tagName },
            success: function(response) {
                if (response.status == 1) {
                    showNotification(`Tag added to ${response.success_count} files`, 'success');
                    loadFiles();
                } else {
                    showNotification('Failed to add tag to files', 'error');
                }
            }
        });
    }

    // Add custom CSS
    $("<style>")
        .prop("type", "text/css")
        .html(`
                    .file-card {
                        transition: transform 0.2s, box-shadow 0.2s;
                        position: relative;
                    }

                    .file-card:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
                    }

                    .file-thumbnail {
                        max-width: 100%;
                        height: 120px;
                        object-fit: cover;
                        border-radius: 4px;
                    }

                    .file-checkbox-container {
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        z-index: 10;
                    }

                    .file-name {
                        font-size: 0.9rem;
                        line-height: 1.3;
                    }

                    .file-actions {
                        opacity: 0;
                        transition: opacity 0.2s;
                    }

                    .file-card:hover .file-actions {
                        opacity: 1;
                    }

                    .upload-progress-container {
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                        background: #f8f9fa;
                    }

                    .dz-upload-progress {
                        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
                        height: 100%;
                        transition: width 0.3s ease;
                    }

                    .dz-error .dz-error-message {
                        background: #f8d7da;
                        color: #721c24;
                        padding: 0.5rem;
                        border-radius: 0.25rem;
                        margin-top: 0.5rem;
                        font-size: 0.875rem;
                    }

                    .dropzone-container {
                        border: 2px dashed #dee2e6;
                        border-radius: 0.375rem;
                        padding: 2rem;
                        text-align: center;
                        transition: all 0.3s ease;
                        background: #f8f9fa;
                    }

                    .dropzone-container:hover {
                        border-color: #0d6efd;
                        background: #f0f8ff;
                    }
                `)
        .appendTo("head");
</script>

<!-- Share QR Modal -->
<div id="share-qr-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h4 class="modal-title font-18 text-white" id="qr-modal-title">Share QR</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="qr-modal-container" class="d-inline-block p-3 bg-white rounded shadow-sm mb-3"></div>
                <div class="mt-2">
                    <h5 class="mb-1 text-dark" id="qr-modal-item-title">Access QR Code</h5>
                    <p class="text-muted mb-3 font-13">Scan this code to access the item on another device.</p>
                    
                    <div class="d-flex justify-content-center gap-3 py-3 border-top border-bottom">
                        <div class="text-center px-2">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold mb-1">Size</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-size">-</span>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center px-2">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold mb-1">Date</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-date">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
