
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
							<h4 class="page-title">P2P Texts</h4>
						</div>
					</div>
				</div>
				<!-- end page title -->
				<div class="row">
					<!-- Left Sidebar -->
					<div class="col-xl-3 col-lg-4">
						<div class="card glass-card">
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
                                    <a href="<?php echo base_url('home/files'); ?>" class="list-group-item border-0">
                                        <i class="mdi mdi-folder-outline font-18 align-middle me-2"></i>
                                        My Files
                                    </a>
									<a href="<?php echo base_url('home/texts'); ?>" class="list-group-item border-0 fw-bolder text-primary">
                                        <i class="mdi mdi-text-box-multiple font-18 align-middle me-2"></i>
                                        Text Data
                                        <span class="badge bg-primary float-end">
                                            <i class="mdi mdi-check-all"></i>
                                        </span>
                                    </a>
									<a href="<?php echo base_url('home/trashed'); ?>" class="list-group-item border-0">
                                        <i class="mdi mdi-trash-can font-18 align-middle me-2"></i>
                                        Trash Files
                                    </a>
								</div>
                                <div class="mt-4 p-3 bg-light rounded text-center">
                                    <h6 class="text-uppercase mb-2">Pair Phone</h6>
                                    <div id="pair-qr" class="d-flex justify-content-center mb-2"></div>
                                    <p class="text-muted font-11 mb-0">Scan to open on mobile</p>
                                </div>
							</div>
						</div>
					</div>
					<!-- end Left Sidebar -->

					<!-- Right Content -->
					<div class="col-xl-9 col-lg-8">
						<!-- Create Text Section -->
						<div class="card glass-card">
							<div class="card-body">
								<h5 class="mb-3">Create a text to copy</h5>
								<div class="row">
									<div class="col-md-8">
										<div class="mb-3">
											<label for="text_title" class="form-label">Text Title (Optional)</label>
											<input type="text" class="form-control" id="text_title" placeholder="Enter a title for your text">
										</div>
									</div>
									<div class="col-md-4">
										<div class="mb-3">
											<label class="form-label">&nbsp;</label>
											<div>
												<button type="button" class="btn btn-success me-2" id="save_text_btn">
													<i class="mdi mdi-content-save"></i> Save Text
												</button>
												<button type="button" class="btn btn-outline-secondary" id="clear_text_btn">
													<i class="mdi mdi-refresh"></i> Clear
												</button>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-content">
									<textarea id="please_type_here" name="text_content"></textarea>
									<!-- end preview-->
								</div>
							</div>
						</div>


						<!-- All Texts Section -->
				<div class="card glass-card">
					<div class="card-body">
						<h5 class="mb-3">All Saved Texts</h5>
						<div class="table-responsive">
							<table id="texts-all-datatable" class="table table-sm table-hover align-middle w-100">
								<thead class="table-light">
									<tr>
										<th>Title</th>
										<th>Date</th>
										<th>Source</th>
										<th>Size</th>
										<th class="text-center no-sort">Actions</th>
									</tr>
								</thead>
								<tbody id="all_texts_container">
									<?php echo $text_list_all ?: '<tr><td colspan="5" class="text-center text-muted">No texts saved yet.</td></tr>'; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
					</div>
					<!-- end Right Content -->
				</div>
				<!-- End row -->
			</div>
			<!-- container -->
		</div>
		<!-- content -->
		<!-- Footer Start -->