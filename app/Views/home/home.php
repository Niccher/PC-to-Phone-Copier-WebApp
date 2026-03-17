
<!-- Start Content - Updated Interface -->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
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
                                <a class="dropdown-item" href="#"><i class="mdi mdi-file-plus-outline me-1"></i> File</a>
                                <a class="dropdown-item" href="#"><i class="mdi mdi-file-document me-1"></i> Dashboard</a>
                            </div>
                        </div>
                        <div class="email-menu-list mt-3">
                            <a href="<?php echo base_url('home/recent'); ?>" class="list-group-item border-0 fw-bolder text-primary">
                                <i class="mdi mdi-history font-18 align-middle me-2"></i>
                                Recent
                                <span class="badge bg-primary float-end">
                                                <i class="mdi mdi-check-all"></i>
                                            </span>
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
                        <!-- Search Bar for Recent Items -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="app-search">
                                <form id="recent-search-form">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" id="recent-search" placeholder="Search recent items...">
                                        <span class="mdi mdi-magnify search-icon"></span>
                                    </div>
                                </form>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2"><?php echo $files_count + $texts_count; ?> total items</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="refreshRecent()" title="Refresh">
                                    <i class="mdi mdi-refresh"></i>
                                </button>
                            </div>
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
                                    <div class="row" id="recent-files-container">
                                        <?php if (!empty($recent_files)): ?>
                                            <?php foreach ($recent_files as $file):
                                                $icon_class = 'file';
                                                if (isset($file->up_file_Extension)) {
                                                    $ext = strtolower($file->up_file_Extension);
                                                    $icon_map = [
                                                        'pdf' => 'file-pdf',
                                                        'doc' => 'file-word',
                                                        'docx' => 'file-word',
                                                        'xls' => 'file-excel',
                                                        'xlsx' => 'file-excel',
                                                        'ppt' => 'file-powerpoint',
                                                        'pptx' => 'file-powerpoint',
                                                        'txt' => 'file-document',
                                                        'jpg' => 'file-image',
                                                        'jpeg' => 'file-image',
                                                        'png' => 'file-image',
                                                        'gif' => 'file-image',
                                                        'mp4' => 'file-video',
                                                        'avi' => 'file-video',
                                                        'zip' => 'folder-zip',
                                                        'rar' => 'folder-zip',
                                                        'mp3' => 'file-music',
                                                        'wav' => 'file-music'
                                                    ];
                                                    $icon_class = isset($icon_map[$ext]) ? $icon_map[$ext] : 'file';
                                                }
                                                $thumbnail = isset($file->up_file_thumbnail) && $file->up_file_thumbnail ? base_url($file->up_file_thumbnail) : '';
                                                ?>
                                                <div class="col-xxl-6 col-lg-12 mb-3">
                                                    <div class="card file-card glass-card h-100">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto">
                                                                    <?php if ($thumbnail): ?>
                                                                        <img src="<?php echo $thumbnail; ?>" class="avatar-sm rounded" alt="Thumbnail">
                                                                    <?php else: ?>
                                                                        <div class="avatar-sm">
																						<span class="avatar-title bg-light text-secondary rounded">
																							<i class="mdi mdi-<?php echo $icon_class; ?> font-16"></i>
																						</span>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col">
                                                                    <h6 class="mb-1 text-truncate">
                                                                        <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>" class="text-reset">
                                                                            <?php echo htmlspecialchars($file->up_file_Orig_Name); ?>
                                                                        </a>
                                                                    </h6>
                                                                    <p class="mb-1 text-muted small">
                                                                        <?php
                                                                        $mod_upload = new \App\Models\ModUpload();
                                                                        echo $mod_upload->bytes_to_human_filesize($file->up_file_Size);
                                                                        ?>
                                                                        • <?php echo date('M j, Y', strtotime($file->up_file_Created_at)); ?>
                                                                    </p>
                                                                    <?php if (isset($file->up_file_tags) && $file->up_file_tags): ?>
                                                                        <div class="file-tags">
                                                                            <?php
                                                                            $tags = explode(',', $file->up_file_tags);
                                                                            foreach (array_slice($tags, 0, 2) as $tag): ?>
                                                                                <span class="badge bg-light text-dark me-1"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                                                            <?php endforeach; ?>
                                                                            <?php if (count($tags) > 2): ?>
                                                                                <span class="badge bg-light text-dark">+<?php echo count($tags) - 2; ?> more</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="dropdown">
                                                                        <a href="#" class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                                                                            <i class="mdi mdi-dots-vertical"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <a href="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>" class="dropdown-item">
                                                                                <i class="mdi mdi-download me-2"></i>Download
                                                                            </a>
                                                                            <a href="javascript:void(0);" class="dropdown-item share-qr-btn" data-url="<?php echo base_url('saved/download/' . $file->up_file_uuid); ?>" data-title="<?php echo htmlspecialchars($file->up_file_Orig_Name); ?>">
                                                                                <i class="mdi mdi-qrcode me-2"></i>Share QR
                                                                            </a>
                                                                            <a href="<?php echo base_url('saved/delete/' . $file->up_file_uuid); ?>" class="dropdown-item text-danger">
                                                                                <i class="mdi mdi-delete me-2"></i>Delete
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <div class="text-center py-4">
                                                    <i class="mdi mdi-file-multiple-outline display-4 text-muted mb-2"></i>
                                                    <h6 class="text-muted">No files uploaded yet</h6>
                                                    <a href="<?php echo base_url('home/files'); ?>" class="btn btn-primary btn-sm">
                                                        <i class="mdi mdi-plus me-1"></i>Upload Files
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Recent Texts Tab -->
                                <div class="tab-pane" id="recent-texts" role="tabpanel">
                                    <div class="row" id="recent-texts-container">
                                        <?php if (!empty($recent_texts)): ?>
                                            <?php foreach ($recent_texts as $text):
                                                $truncated_content = strlen($text->text_content) > 150 ?
                                                    substr($text->text_content, 0, 150) . '...' : $text->text_content;
                                                $title = isset($text->text_title) && $text->text_title ? $text->text_title : 'Untitled Text';
                                                ?>
                                                <div class="col-xxl-6 col-lg-12 mb-3">
                                                    <div class="card text-card glass-card h-100">
                                                        <div class="card-body">
                                                            <div class="row align-items-start">
                                                                <div class="col-auto">
                                                                    <div class="avatar-sm">
																					<span class="avatar-title bg-success text-white rounded">
																						<i class="mdi mdi-text font-16"></i>
																					</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <h6 class="mb-1">
                                                                        <a href="javascript:void(0);" class="text-reset copy-text-link" data-text="<?php echo htmlspecialchars($text->text_content); ?>">
                                                                            <?php echo htmlspecialchars($title); ?>
                                                                        </a>
                                                                    </h6>
                                                                    <p class="mb-2 text-muted small">
                                                                        <?php echo strlen($text->text_content); ?> characters
                                                                        • <?php echo date('M j, Y', strtotime($text->text_created_at)); ?>
                                                                    </p>
                                                                    <div class="text-preview">
                                                                        <small class="text-muted">
                                                                            <?php echo htmlspecialchars($truncated_content); ?>
                                                                        </small>
                                                                    </div>
                                                                    <?php if (isset($text->text_tags) && $text->text_tags): ?>
                                                                        <div class="file-tags mt-2">
                                                                            <?php
                                                                            $tags = explode(',', $text->text_tags);
                                                                            foreach (array_slice($tags, 0, 2) as $tag): ?>
                                                                                <span class="badge bg-light text-dark me-1"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                                                            <?php endforeach; ?>
                                                                            <?php if (count($tags) > 2): ?>
                                                                                <span class="badge bg-light text-dark">+<?php echo count($tags) - 2; ?> more</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="dropdown">
                                                                        <a href="#" class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                                                                            <i class="mdi mdi-dots-vertical"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <a href="javascript:void(0);" class="dropdown-item copy-text-link" data-text="<?php echo htmlspecialchars($text->text_content); ?>">
                                                                                <i class="mdi mdi-content-copy me-2"></i>Copy Text
                                                                            </a>
                                                                            <a href="javascript:void(0);" class="dropdown-item share-qr-btn" data-url="<?php echo base_url('home/texts?view=' . $text->text_uuid); ?>" data-title="<?php echo htmlspecialchars($title); ?>">
                                                                                <i class="mdi mdi-qrcode me-2"></i>Share QR
                                                                            </a>
                                                                            <a href="<?php echo base_url('text/delete/' . $text->text_uuid); ?>" class="dropdown-item text-danger">
                                                                                <i class="mdi mdi-delete me-2"></i>Delete
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <div class="text-center py-4">
                                                    <i class="mdi mdi-text-box-multiple-outline display-4 text-muted mb-2"></i>
                                                    <h6 class="text-muted">No texts saved yet</h6>
                                                    <a href="<?php echo base_url('home/texts'); ?>" class="btn btn-success btn-sm">
                                                        <i class="mdi mdi-plus me-1"></i>Create Text
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
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