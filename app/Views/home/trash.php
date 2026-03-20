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
                            <li class="breadcrumb-item active">Trash</li>
                        </ol>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#global-upload-modal">
                            <i class="mdi mdi-plus me-1"></i>Upload / Add Text
                        </button>
                    </div>
                </div>
                <h4 class="page-title">Trash</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <!-- Right Content -->
        <div class="col-12">
            <div class="card glass-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0">
                                <i class="mdi mdi-trash-can-outline me-2"></i>Deleted Items
                            </h5>
                            <p class="text-muted mb-0 small">Items in trash will be permanently deleted after 30 days</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning me-3"><?php echo $total_deleted; ?> items</span>
                            <?php if ($total_deleted > 0): ?>
                                <button class="btn btn-danger btn-sm shadow-sm" onclick="emptyTrash()">
                                    <i class="mdi mdi-delete-sweep me-1"></i>Empty Trash
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Trash Items Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle w-100" id="trash-table">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 40px"></th>
                                <th>Name</th>
                                <th>Details</th>
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($deleted_files) || !empty($deleted_texts)): ?>

                                <!-- Deleted Files -->
                                <?php if (!empty($deleted_files)): ?>
                                    <?php 
                                    $mod_upload = new \App\Models\ModUpload();
                                    foreach ($deleted_files as $file): 
                                        $ext = strtolower($file->up_file_Extension ?? '');
                                        $iconData = $mod_upload->getFileIconClass($ext);
                                    ?>
                                        <tr class="file-row">
                                            <td class="text-center">
                                                <span class="file-icon-box" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(0,0,0,0.04);">
                                                    <i class="mdi mdi-<?php echo $iconData['icon']; ?> font-20 <?php echo $iconData['color']; ?>"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <h6 class="mb-0 text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($file->up_file_Orig_Name); ?>">
                                                    <?php echo htmlspecialchars($file->up_file_Orig_Name); ?>
                                                </h6>
                                                <small class="text-muted">File • <?php echo isset($file->up_file_Extension) ? strtoupper($file->up_file_Extension) : 'Unknown'; ?></small>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <div><i class="mdi mdi-weight me-1"></i><?php echo isset($file->up_file_Size) ? $mod_upload->bytes_to_human_filesize($file->up_file_Size) : 'Unknown'; ?></div>
                                                    <div><i class="mdi mdi-earth me-1"></i><?php echo isset($file->up_file_Source) ? str_replace("Upload", "", $file->up_file_Source) : 'Unknown'; ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php
                                                    $deleted_at_ts = isset($file->deleted_at) && $file->deleted_at && $file->deleted_at != '0000-00-00 00:00:00'
                                                        ? strtotime($file->deleted_at)
                                                        : (isset($file->up_file_Created_at) ? strtotime($file->up_file_Created_at) : time());
                                                    echo date('M j, Y H:i', $deleted_at_ts);
                                                    ?>
                                                </div>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <button class="btn btn-sm btn-outline-success py-0 px-2 me-1" onclick="restoreFile('<?php echo $file->up_file_uuid; ?>')" title="Restore">
                                                    <i class="mdi mdi-restore me-1"></i>Restore
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="permanentDeleteFile('<?php echo $file->up_file_uuid; ?>')" title="Delete Forever">
                                                    <i class="mdi mdi-delete-forever me-1"></i>Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Deleted Texts -->
                                <?php if (!empty($deleted_texts)): ?>
                                    <?php foreach ($deleted_texts as $text): ?>
                                        <tr class="text-row">
                                            <td class="text-center">
                                                <span class="file-icon-box" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(40, 167, 69, 0.1);">
                                                    <i class="mdi mdi-text-box-outline font-20 text-success"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <h6 class="mb-0 text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($text->text_title ?: 'Untitled Text'); ?>">
                                                    <?php echo htmlspecialchars($text->text_title ?: 'Untitled Text'); ?>
                                                </h6>
                                                <small class="text-muted">Text • <?php echo strlen($text->text_content); ?> chars</small>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <div class="text-truncate" style="max-width: 150px;">
                                                        <i class="mdi mdi-eye-outline me-1"></i><?php echo htmlspecialchars(substr($text->text_content, 0, 30)); ?>...
                                                    </div>
                                                    <div><i class="mdi mdi-earth me-1"></i><?php echo isset($text->text_source) ? str_replace("Text", "", $text->text_source) : 'Browser'; ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <?php
                                                    $deleted_at_ts = isset($text->deleted_at) && $text->deleted_at && $text->deleted_at != '0000-00-00 00:00:00'
                                                        ? strtotime($text->deleted_at)
                                                        : (isset($text->text_created_at) ? strtotime($text->text_created_at) : time());
                                                    echo date('M j, Y H:i', $deleted_at_ts);
                                                    ?>
                                                </div>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <button class="btn btn-sm btn-outline-success py-0 px-2 me-1" onclick="restoreText('<?php echo $text->text_uuid; ?>')" title="Restore">
                                                    <i class="mdi mdi-restore me-1"></i>Restore
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="permanentDeleteText('<?php echo $text->text_uuid; ?>')" title="Delete Forever">
                                                    <i class="mdi mdi-delete-forever me-1"></i>Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <span class="avatar-title bg-light text-muted rounded-circle display-4">
                                                <i class="mdi mdi-delete-empty"></i>
                                            </span>
                                        </div>
                                        <h5 class="text-muted">Trash is empty</h5>
                                        <p class="text-muted mb-0">Deleted files and texts will appear here</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_deleted > 0): ?>
                        <div class="alert alert-info bg-info bg-opacity-10 border-info mt-3 py-2 px-3">
                            <p class="text-info mb-0 small">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Items are automatically deleted after 30 days. Showing <?php echo $total_deleted; ?> deleted items.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- container -->
