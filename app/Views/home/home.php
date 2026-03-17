
<!-- Start Content - Updated Interface -->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Recent</li>
                    </ol>
                </div>
                <h4 class="page-title">P2P Manager</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <!-- Right Sidebar -->
        <div class="col-12">
            <div class="card glass-card">
                <div class="card-body">
                    <!-- Left sidebar -->
                    <div class="page-aside-left">
                        <div class="btn-group d-block mb-2">
                            <button type="button" class="btn btn-success dropdown-toggle w-100" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="mdi mdi-plus"></i> Create New </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo base_url('home/files'); ?>"><i class="mdi mdi-file-plus-outline me-1"></i> File</a>
                                <a class="dropdown-item" href="<?php echo base_url('home/texts'); ?>"><i class="mdi mdi-file-document me-1"></i> Text</a>
                            </div>
                        </div>
                        <div class="email-menu-list mt-3">
                            <a href="<?php echo base_url('home/recent'); ?>" class="list-group-item border-0 fw-bolder text-primary">
                                <i class="mdi mdi-history font-18 align-middle me-2"></i>
                                Recent
                                <span class="badge bg-primary float-end"><i class="mdi mdi-check-all"></i></span>
                            </a>
                            <a href="<?php echo base_url('home/files'); ?>" class="list-group-item border-0">
                                <i class="mdi mdi-folder-outline font-18 align-middle me-2"></i>
                                My Files
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
                        <div class="mt-4 p-3 bg-light rounded text-center">
                            <h6 class="text-uppercase mb-2">Pair Phone</h6>
                            <div id="pair-qr" class="d-flex justify-content-center mb-2"></div>
                            <p class="text-muted font-11 mb-0">Scan to open on mobile</p>
                        </div>
                    </div>
                    <!-- End Left sidebar -->
                    <div class="page-aside-right">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-info me-2"><?php echo $files_count + $texts_count; ?> total items</span>
                            </div>
                            <a href="<?php echo base_url('home/files'); ?>" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-upload me-1"></i>Upload Files
                            </a>
                        </div>

                        <!-- Recent Items Tabs -->
                        <div class="recent-tabs">
                            <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="files-tab" data-bs-toggle="tab" href="#recent-files" role="tab">
                                        <i class="mdi mdi-file-multiple me-1"></i>
                                        Files
                                        <span class="badge bg-primary ms-1"><?php echo $files_count; ?></span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="texts-tab" data-bs-toggle="tab" href="#recent-texts" role="tab">
                                        <i class="mdi mdi-text-box-multiple me-1"></i>
                                        Texts
                                        <span class="badge bg-success ms-1"><?php echo $texts_count; ?></span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Recent Files Tab -->
                                <div class="tab-pane show active" id="recent-files" role="tabpanel">
                                    <?php if (!empty($recent_files)): ?>
                                    <div class="table-responsive">
                                        <table id="files-datatable" class="table table-sm table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:36px"></th>
                                                    <th>File Name</th>
                                                    <th>Size</th>
                                                    <th>Dimensions</th>
                                                    <th>Date</th>
                                                    <th>Source</th>
                                                    <th class="text-center no-sort">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $mod_upload = new \App\Models\ModUpload();
                                                foreach ($recent_files as $file):
                                                    $ext = strtolower($file->up_file_Extension ?? '');
                                                    $iconData = $mod_upload->getFileIconClass($ext);
                                                    $thumbnail = !empty($file->up_file_thumbnail) ? base_url($file->up_file_thumbnail) : '';
                                                    $imageExts = ['jpg','jpeg','png','gif','webp','bmp'];
                                                    $isImage = in_array($ext, $imageExts);
                                                    $dimensions = '';
                                                    if (!empty($file->up_file_width) && !empty($file->up_file_height)) {
                                                        $dimensions = $file->up_file_width . ' × ' . $file->up_file_height;
                                                    }
                                                    $fileSize = $mod_upload->bytes_to_human_filesize($file->up_file_Size ?? 0);
                                                    $fileDate = date('M j, Y H:i', strtotime($file->up_file_Created_at ?? 'now'));
                                                    $source = $file->up_file_Source ?? 'Browser';
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?php if ($isImage && $thumbnail): ?>
                                                            <img src="<?php echo $thumbnail; ?>" class="rounded" style="width:32px;height:32px;object-fit:cover;" alt="thumb">
                                                        <?php else: ?>
                                                            <span class="avatar-title rounded <?php echo str_replace('text-','bg-',str_replace('muted','light',$iconData['color'])); ?> bg-opacity-25" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;">
                                                                <i class="mdi mdi-<?php echo $iconData['icon']; ?> font-16 <?php echo $iconData['color']; ?>"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>" class="fw-semibold text-reset">
                                                            <?php echo htmlspecialchars($file->up_file_Orig_Name ?? ''); ?>
                                                        </a>
                                                        <?php if (!empty($ext)): ?>
                                                            <span class="badge bg-light text-dark ms-1 text-uppercase font-10"><?php echo $ext; ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-nowrap text-muted"><?php echo $fileSize; ?></td>
                                                    <td class="text-muted"><?php echo $dimensions ?: '—'; ?></td>
                                                    <td class="text-nowrap text-muted"><?php echo $fileDate; ?></td>
                                                    <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($source); ?></span></td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>" class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="Download">
                                                            <i class="mdi mdi-download"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-info py-0 px-2 me-1 share-qr-btn"
                                                            data-url="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>"
                                                            data-title="<?php echo htmlspecialchars($file->up_file_Orig_Name ?? ''); ?>"
                                                            data-size="<?php echo $fileSize; ?>"
                                                            data-date="<?php echo $fileDate; ?>"
                                                            title="Share QR">
                                                            <i class="mdi mdi-qrcode"></i>
                                                        </a>
                                                        <a href="<?php echo base_url('saved/delete/' . $file->up_file_uuid); ?>" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete" onclick="return confirm('Delete this file?')">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-file-multiple-outline display-4 text-muted mb-2"></i>
                                        <h6 class="text-muted">No files uploaded yet</h6>
                                        <a href="<?php echo base_url('home/files'); ?>" class="btn btn-primary btn-sm">
                                            <i class="mdi mdi-plus me-1"></i>Upload Files
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Recent Texts Tab -->
                                <div class="tab-pane" id="recent-texts" role="tabpanel">
                                    <?php if (!empty($recent_texts)): ?>
                                    <div class="table-responsive">
                                        <table id="texts-datatable" class="table table-sm table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Size</th>
                                                    <th>Date</th>
                                                    <th>Source</th>
                                                    <th class="text-center no-sort">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_texts as $text):
                                                    $textTitle = !empty($text->text_title) ? $text->text_title : 'Untitled Text';
                                                    $textSize = strlen($text->text_content ?? '') . ' chars';
                                                    $textDate = date('M j, Y H:i', strtotime($text->text_created_at ?? 'now'));
                                                    $textSource = str_replace('Text', '', $text->text_source ?? 'Browser');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="javascript:void(0);" class="fw-semibold text-reset copy-text-link" data-text="<?php echo htmlspecialchars($text->text_content ?? ''); ?>">
                                                            <?php echo htmlspecialchars($textTitle); ?>
                                                        </a>
                                                    </td>
                                                    <td class="text-muted"><?php echo $textSize; ?></td>
                                                    <td class="text-nowrap text-muted"><?php echo $textDate; ?></td>
                                                    <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($textSource); ?></span></td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary py-0 px-2 me-1 copy-text-link"
                                                            data-text="<?php echo htmlspecialchars($text->text_content ?? ''); ?>" title="Copy">
                                                            <i class="mdi mdi-content-copy"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-info py-0 px-2 me-1 share-qr-btn"
                                                            data-url="<?php echo base_url('home/texts?view=' . $text->text_uuid); ?>"
                                                            data-title="<?php echo htmlspecialchars($textTitle); ?>"
                                                            data-size="<?php echo $textSize; ?>"
                                                            data-date="<?php echo $textDate; ?>"
                                                            title="Share QR">
                                                            <i class="mdi mdi-qrcode"></i>
                                                        </a>
                                                        <a href="<?php echo base_url('text/delete/' . $text->text_uuid); ?>" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete" onclick="return confirm('Delete this text?')">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-text-box-multiple-outline display-4 text-muted mb-2"></i>
                                        <h6 class="text-muted">No texts saved yet</h6>
                                        <a href="<?php echo base_url('home/texts'); ?>" class="btn btn-success btn-sm">
                                            <i class="mdi mdi-plus me-1"></i>Create Text
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end inbox-rightbar-->
                </div>
                <!-- end card-body -->
                <div class="clearfix"></div>
            </div>
            <!-- end card-box -->
        </div>
        <!-- end Col -->
    </div>
    <!-- End row -->
</div>
<!-- container -->
</div>
<!-- content -->
<!-- Footer Start -->