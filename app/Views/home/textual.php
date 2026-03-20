
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
                                        <li class="breadcrumb-item active">Texts</li>
                                    </ol>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#global-upload-modal">
                                        <i class="mdi mdi-plus me-1"></i>Upload / Add Text
                                    </button>
                                </div>
							</div>
							<h4 class="page-title">P2P Texts</h4>
						</div>
					</div>
				</div>
				<!-- end page title -->
				<div class="row">
					<!-- Right Content -->
					<div class="col-12">
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
												<button type="button" class="btn btn-outline-primary" id="paste_text_btn">
													<i class="mdi mdi-clipboard-text"></i> Paste & Save
												</button>
												<button type="button" class="btn btn-outline-secondary" id="clear_text_btn">
													<i class="mdi mdi-refresh"></i> Clear
												</button>
											</div>
										</div>
									</div>
								</div>
								<div class="mt-2 text-editor-wrapper">
									<textarea id="please_type_here" name="text_content"></textarea>
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
									<?php echo $text_list_all ?: '<tr><td>No texts saved yet.</td><td></td><td></td><td></td><td></td></tr>'; ?>
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