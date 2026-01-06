
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Texts</li>
                    </ol>
                </div>
                <h4 class="page-title">P2P Files</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-xl-3 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group d-block mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle w-100" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-plus"></i> Create New
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo base_url('home/files'); ?>">
                                <i class="mdi mdi-file-plus-outline me-1"></i>
                                File
                            </a>
                            <a class="dropdown-item" href="<?php echo base_url('home/texts'); ?>">
                                <i class="mdi mdi-file-document me-1"></i>
                                Text
                            </a>
                        </div>
                    </div>
                    <div class="email-menu-list mt-3">
                        <a href="<?php echo base_url('home/recent'); ?>" class="list-group-item border-0">
                            <i class="mdi mdi-history font-18 align-middle me-2"></i>
                            Recent
                        </a>
                        <a href="<?php echo base_url('home/files'); ?>" class="list-group-item border-0 fw-bolder text-primary">
                            <i class="mdi mdi-folder-outline font-18 align-middle me-2"></i>
                            My Files
                            <span class="badge bg-primary float-end">
                                            <i class="mdi mdi-check-all"></i>
                                        </span>
                        </a>
                        <a href="<?php echo base_url('home/texts'); ?>" class="list-group-item border-0">
                            <i class="mdi mdi-text-box-multiple font-18 align-middle me-2"></i>
                            Text Data
                        </a>
                        <a href="<?php echo base_url('home/trashed'); ?>" class="list-group-item border-0">
                            <i class="mdi mdi-trash-can font-18 align-middle me-2"></i>
                            Trash Files
                        </a>
                    </div>
                    <div class="mt-5">
                        <h6 class="text-uppercase mt-3">Storage</h6>
                        <div class="progress my-2 progress-sm">
                            <div class="progress-bar progress-lg bg-success" role="progressbar" style="width: 46%" aria-valuenow="46" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted font-12 mb-0">7.02 GB (46%) of 15 GB used</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Left Sidebar -->

        <!-- Right Content -->
        <div class="col-xl-9 col-lg-8">
            <!-- Upload Section -->
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="mdi mdi-cloud-upload me-2"></i>
                        Upload Files
                        <span class="badge bg-info ms-2" id="upload-queue-count">0 in queue</span>
                    </h5>

                    <!-- Upload Info -->
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Upload Guidelines:</strong> Maximum file size: 50MB. Supported formats: Documents, Images, Archives, Audio, Video, and Code files.
                    </div>

                    <!-- Upload Zone -->
                    <div class="tab-content">
                        <div class="dropzone-container">
                            <form action="<?php echo base_url('home/file/upload'); ?>" class="dropzone" id="file-upload-dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                                <div class="dz-message needsclick">
                                    <div class="upload-icon">
                                        <i class="mdi mdi-cloud-upload display-4 text-primary"></i>
                                    </div>
                                    <h4>Drop files here or click to upload</h4>
                                    <p class="text-muted">You can upload multiple files at once. Files will be processed in queue.</p>
                                    <div class="upload-limits">
                                        <small class="text-muted">
                                            <i class="mdi mdi-shield-check me-1"></i>
                                            Max 50MB per file • Secure encrypted transfer
                                        </small>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Upload Progress -->
                        <div class="upload-progress-container mt-3" id="upload-progress-container" style="display: none;">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-progress-upload me-2"></i>
                                        Upload Progress
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="overall-progress mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold">Overall Progress</span>
                                            <span class="text-muted" id="overall-progress-text">0%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                 id="overall-progress-bar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div id="file-progress-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Management Section -->
            <div class="card">
                <div class="card-body">
                    <!-- Header with Search and Actions -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="mdi mdi-folder-multiple me-2"></i>
                            File Manager
                            <span class="badge bg-secondary ms-2" id="file-count">0 files</span>
                        </h5>

                        <!-- Batch Actions (hidden by default) -->
                        <div class="batch-actions" id="batch-actions" style="display: none;">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" id="batch-select-all">
                                    <i class="mdi mdi-check-all"></i> Select All
                                </button>
                                <button class="btn btn-sm btn-outline-danger" id="batch-delete">
                                    <i class="mdi mdi-delete-multiple"></i> Delete Selected
                                </button>
                                <button class="btn btn-sm btn-outline-info" id="batch-add-tag">
                                    <i class="mdi mdi-tag-plus"></i> Add Tag
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" id="file-search" placeholder="Search files...">
                                <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="category-filter">
                                    <option value="">All Categories</option>
                                </select>
                                <select class="form-select form-select-sm" id="type-filter">
                                    <option value="">All Types</option>
                                    <option value="pdf">PDF</option>
                                    <option value="doc">Word</option>
                                    <option value="jpg">Images</option>
                                    <option value="mp4">Videos</option>
                                    <option value="zip">Archives</option>
                                </select>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="mdi mdi-view-grid"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item view-toggle active" data-view="grid" href="#"><i class="mdi mdi-view-grid me-2"></i>Grid View</a></li>
                                        <li><a class="dropdown-item view-toggle" data-view="list" href="#"><i class="mdi mdi-view-list me-2"></i>List View</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Files Container -->
                    <div id="files-container">
                        <div class="text-center py-5">
                            <i class="mdi mdi-folder-open display-4 text-muted"></i>
                            <h5 class="text-muted mt-2">No files found</h5>
                            <p class="text-muted">Upload some files or adjust your search criteria</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end Right Content -->
    </div>

    <!-- File Preview Modal -->
    <div class="modal fade" id="file-preview-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="file-preview-title">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- File Preview -->
                        <div class="col-md-8">
                            <div class="file-preview-container text-center">
                                <div id="file-preview-content">
                                    <i class="mdi mdi-file-document-outline display-4 text-muted"></i>
                                    <p class="text-muted mt-2">Loading preview...</p>
                                </div>
                            </div>
                        </div>

                        <!-- File Details -->
                        <div class="col-md-4">
                            <div class="file-details">
                                <h6>File Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td id="file-detail-name">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Size:</strong></td>
                                        <td id="file-detail-size">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td id="file-detail-type">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td id="file-detail-category">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Uploaded:</strong></td>
                                        <td id="file-detail-date">-</td>
                                    </tr>
                                </table>

                                <!-- Tags -->
                                <div class="mt-3">
                                    <h6>Tags</h6>
                                    <div id="file-tags-container">
                                        <small class="text-muted">No tags</small>
                                    </div>
                                    <div class="mt-2">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="new-tag-input" placeholder="Add tag">
                                            <button class="btn btn-outline-primary" type="button" id="add-tag-btn">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mt-3">
                                    <h6>Description</h6>
                                    <textarea class="form-control form-control-sm" id="file-description" rows="3" placeholder="Add a description..."></textarea>
                                    <button class="btn btn-sm btn-outline-primary mt-2" id="save-description-btn">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" id="download-file-btn">
                            <i class="mdi mdi-download me-1"></i>Download
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="rename-file-btn">
                            <i class="mdi mdi-pencil me-1"></i>Rename
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="delete-file-btn">
                            <i class="mdi mdi-delete me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename File Modal -->
    <div class="modal fade" id="rename-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rename-input" class="form-label">New filename</label>
                        <input type="text" class="form-control" id="rename-input" placeholder="Enter new filename">
                        <div class="form-text">Include file extension</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-rename-btn">Rename</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End row -->
</div>
<!-- container -->
</div>
<!-- content -->
<!-- Footer Start -->