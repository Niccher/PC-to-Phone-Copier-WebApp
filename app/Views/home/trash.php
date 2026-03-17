<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Trash</li>
                    </ol>
                </div>
                <h4 class="page-title">Trash</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">Deleted Items</h5>
                            <p class="text-muted mb-0">Items in trash will be permanently deleted after 30 days</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning me-3"><?php echo $total_deleted; ?> items in trash</span>
                            <button class="btn btn-outline-danger btn-sm" onclick="emptyTrash()">
                                <i class="mdi mdi-delete-sweep me-1"></i>Empty Trash
                            </button>
                        </div>
                    </div>

                    <!-- Trash Items Table -->
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0" id="trash-table">
                            <thead class="table-light">
                            <tr>
                                <th class="border-0">Type</th>
                                <th class="border-0">Name</th>
                                <th class="border-0">Details</th>
                                <th class="border-0">Original Upload</th>
                                <th class="border-0">Deleted</th>
                                <th class="border-0">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($deleted_files) || !empty($deleted_texts)): ?>

                                <!-- Deleted Files -->
                                <?php if (!empty($deleted_files)): ?>
                                    <?php foreach ($deleted_files as $file): ?>
                                        <tr class="file-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
																		<span class="avatar-title bg-primary text-white rounded">
																			<i class="mdi mdi-file"></i>
																		</span>
                                                    </div>
                                                    <span class="badge bg-primary">File</span>
                                                </div>
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($file->up_file_Orig_Name); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($file->up_file_Name); ?></small>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <div><strong>Size:</strong> <?php echo isset($file->up_file_Size) ? (new \App\Models\ModUpload())->bytes_to_human_filesize($file->up_file_Size) : 'Unknown'; ?></div>
                                                    <div><strong>Type:</strong> <?php echo isset($file->up_file_Extension) ? strtoupper($file->up_file_Extension) : 'Unknown'; ?></div>
                                                    <div><strong>Source:</strong> <?php echo isset($file->up_file_Source) ? str_replace("Upload", "", $file->up_file_Source) : 'Unknown'; ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php echo isset($file->up_file_Created_at) ? date('M j, Y H:i', strtotime($file->up_file_Created_at)) : 'Unknown'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php
                                                    $deleted_at = isset($file->deleted_at) && $file->deleted_at && $file->deleted_at != '0000-00-00 00:00:00'
                                                        ? strtotime($file->deleted_at)
                                                        : time();
                                                    echo date('M j, Y H:i', $deleted_at);
                                                    ?>
                                                </div>
                                                <small class="text-warning">
                                                    <?php
                                                    $days_since = floor((time() - $deleted_at) / (60*60*24));
                                                    echo $days_since > 0 ? "{$days_since} days ago" : "Today";
                                                    ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="#" class="dropdown-item text-success" onclick="restoreFile('<?php echo $file->up_file_uuid; ?>')">
                                                            <i class="mdi mdi-restore me-2"></i>Restore
                                                        </a>
                                                        <a href="#" class="dropdown-item text-danger" onclick="permanentDeleteFile('<?php echo $file->up_file_uuid; ?>')">
                                                            <i class="mdi mdi-delete-forever me-2"></i>Delete Forever
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Deleted Texts -->
                                <?php if (!empty($deleted_texts)): ?>
                                    <?php foreach ($deleted_texts as $text): ?>
                                        <tr class="text-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
																		<span class="avatar-title bg-success text-white rounded">
																			<i class="mdi mdi-text"></i>
																		</span>
                                                    </div>
                                                    <span class="badge bg-success">Text</span>
                                                </div>
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($text->text_title ?: 'Untitled Text'); ?></h6>
                                                <small class="text-muted">UUID: <?php echo htmlspecialchars($text->text_uuid); ?></small>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <div><strong>Length:</strong> <?php echo strlen($text->text_content); ?> characters</div>
                                                    <div><strong>Source:</strong> <?php echo isset($text->text_source) ? str_replace("Text", "", $text->text_source) : 'Browser'; ?></div>
                                                    <div><strong>Preview:</strong>
                                                        <small><?php echo htmlspecialchars(substr($text->text_content, 0, 50)); ?><?php echo strlen($text->text_content) > 50 ? '...' : ''; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php echo isset($text->text_created_at) ? date('M j, Y H:i', strtotime($text->text_created_at)) : 'Unknown'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php
                                                    $deleted_at = isset($text->deleted_at) && $text->deleted_at && $text->deleted_at != '0000-00-00 00:00:00'
                                                        ? strtotime($text->deleted_at)
                                                        : time();
                                                    echo date('M j, Y H:i', $deleted_at);
                                                    ?>
                                                </div>
                                                <small class="text-warning">
                                                    <?php
                                                    $days_since = floor((time() - $deleted_at) / (60*60*24));
                                                    echo $days_since > 0 ? "{$days_since} days ago" : "Today";
                                                    ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="#" class="dropdown-item text-success" onclick="restoreText('<?php echo $text->text_uuid; ?>')">
                                                            <i class="mdi mdi-restore me-2"></i>Restore
                                                        </a>
                                                        <a href="#" class="dropdown-item text-danger" onclick="permanentDeleteText('<?php echo $text->text_uuid; ?>')">
                                                            <i class="mdi mdi-delete-forever me-2"></i>Delete Forever
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="mdi mdi-delete-empty display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">Trash is empty</h5>
                                        <p class="text-muted mb-0">Deleted files and texts will appear here</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination could be added here if needed -->
                    <?php if ($total_deleted > 0): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Showing <?php echo $total_deleted; ?> deleted item<?php echo $total_deleted > 1 ? 's' : ''; ?>
                            </div>
                            <div class="text-muted small">
                                Items are automatically deleted after 30 days
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- container -->
