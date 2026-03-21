<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <div class="d-flex align-items-center">
                        <ol class="breadcrumb m-0 me-3">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                            <li class="breadcrumb-item active">My Files</li>
                        </ol>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal"
                            data-bs-target="#global-upload-modal">
                            <i class="mdi mdi-plus me-1"></i>Upload / Add Text
                        </button>
                    </div>
                </div>
                <h4 class="page-title">P2P Files</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <!-- Right Content -->
        <div class="col-12">

            <!-- Upload Section -->
            <div class="card glass-card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="mdi mdi-cloud-upload me-2"></i>Upload Files
                        <span class="badge bg-info ms-2" id="upload-queue-count">0 in queue</span>
                    </h5>
                    <div class="alert alert-info py-2 mb-3">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Max 50MB per file.</strong> Supported: Documents, Images, Archives, Audio, Video, Code.
                    </div>
                    <div class="dropzone-container">
                        <form action="<?php echo base_url('home/file/upload'); ?>" class="dropzone"
                            id="file-upload-dropzone">
                            <div class="fallback"><input name="file" type="file" multiple /></div>
                            <div class="dz-message needsclick">
                                <div class="upload-icon"><i class="mdi mdi-cloud-upload display-4 text-primary"></i>
                                </div>
                                <h5>Drop files here or click to upload</h5>
                                <p class="text-muted">Multiple files supported</p>
                            </div>
                        </form>
                        <div class="mt-3">
                            <label class="form-label fw-semibold small">Expiration Policy</label>
                            <select class="form-select form-select-sm" id="page-file-expiration">
                                <option value="0">Keep Forever</option>
                                <option value="1">Expire in 1 Hour</option>
                                <option value="2">Burn After Reading</option>
                            </select>
                        </div>
                    </div>
                    <div class="upload-progress-container mt-3" id="upload-progress-container" style="display: none;">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="mdi mdi-progress-upload me-2"></i>Upload Progress</h6>
                            </div>
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">Overall</span>
                                    <span class="text-muted" id="overall-progress-text">0%</span>
                                </div>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        id="overall-progress-bar" style="width: 0%"></div>
                                </div>
                                <div id="file-progress-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Management Table -->
            <div class="card glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="mdi mdi-folder-multiple me-2"></i>All Files
                            <span class="badge bg-secondary ms-2">
                                <?php echo count($files ?? []); ?> files
                            </span>
                        </h5>

                        <div id="batch-actions" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" id="batch-download-zip">
                                <i class="mdi mdi-zip-box"></i> Download ZIP
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info me-1" id="batch-add-tag-btn"
                                data-bs-toggle="modal" data-bs-target="#add-tag-modal">
                                <i class="mdi mdi-tag-plus"></i> Tag
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="batch-delete">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($files)): ?>
                    <div class="table-responsive">
                        <table id="files-manager-table" class="table table-sm table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px"><input type="checkbox" class="form-check-input"
                                            id="select-all-files"></th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Source</th>
                                    <th class="text-center no-sort">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
    $mod_upload = new \App\Models\ModUpload();
    foreach ($files as $file):
        $ext = strtolower($file->up_file_Extension ?? '');
        $iconData = $mod_upload->getFileIconClass($ext);
        $thumbnail = !empty($file->up_file_thumbnail) ? base_url($file->up_file_thumbnail) : '';
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $isImage = in_array($ext, $imageExts);
        $dimensions = '';
        if (!empty($file->up_file_width) && !empty($file->up_file_height)) {
            $dimensions = $file->up_file_width . ' × ' . $file->up_file_height;
        }
        $fileSize = $mod_upload->bytes_to_human_filesize($file->up_file_Size ?? 0);
        $fileSizeRaw = $file->up_file_Size ?? 0;
        $fileDate = date('M j, Y H:i', strtotime($file->up_file_Created_at ?? 'now'));
        $source = htmlspecialchars($file->up_file_Source ?? 'Browser');
        $category = htmlspecialchars($file->up_file_category ?? 'Other');
?>
                                <tr>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="checkbox" class="form-check-input file-checkbox me-2"
                                                value="<?php echo $file->up_file_uuid; ?>">
                                            <span class="file-icon-box"
                                                style="width:42px;height:42px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:rgba(0,0,0,0.04);transition: all 0.2s;">
                                                <i
                                                    class="mdi mdi-<?php echo $iconData['icon']; ?> font-24 <?php echo $iconData['color']; ?>"></i>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-truncate" style="max-width: 250px;"
                                            title="<?php echo htmlspecialchars($file->up_file_Orig_Name ?? ''); ?>">
                                            <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>"
                                                class="text-reset">
                                                <?php echo htmlspecialchars($file->up_file_Orig_Name ?? ''); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">File •
                                            <?php echo !empty($ext) ? strtoupper($ext) : 'Unknown'; ?>
                                        </small>
                                    </td>
                                    <td class="text-nowrap text-muted" data-order="<?php echo $fileSizeRaw; ?>">
                                        <?php echo $fileSize; ?>
                                    </td>
                                    <td><span class="badge bg-info bg-opacity-25 text-white">
                                            <?php echo $category; ?>
                                        </span></td>
                                    <td class="text-nowrap text-muted">
                                        <?php echo $fileDate; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark">
                                            <?php echo $source; ?>
                                        </span></td>
                                    <td class="text-center text-nowrap">
                                        <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>"
                                            class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="Download">
                                            <i class="mdi mdi-download"></i>
                                        </a>
                                        <a href="javascript:void(0);"
                                            class="btn btn-sm btn-outline-info py-0 px-2 me-1 share-qr-btn"
                                            data-url="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>"
                                            data-title="<?php echo htmlspecialchars($file->up_file_Orig_Name ?? ''); ?>"
                                            data-size="<?php echo $fileSize; ?>" data-date="<?php echo $fileDate; ?>"
                                            title="Share QR">
                                            <i class="mdi mdi-qrcode"></i>
                                        </a>
                                        <a href="<?php echo base_url('saved/delete/' . $file->up_file_uuid); ?>"
                                            class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete"
                                            onclick="return confirm('Delete \'<?php echo addslashes($file->up_file_Orig_Name ?? ''); ?>\'?')">
                                            <i class="mdi mdi-delete"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
    endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
else: ?>
                    <div class="text-center py-5">
                        <i class="mdi mdi-folder-open display-4 text-muted mb-2"></i>
                        <h5 class="text-muted">No files yet</h5>
                        <p class="text-muted">Upload files using the dropzone above</p>
                    </div>
                    <?php
endif; ?>
                </div>
            </div>

        </div>
        <!-- end Right Content -->
    </div>
</div>
<!-- container -->
</div>
<!-- content -->
<!-- Footer Start -->