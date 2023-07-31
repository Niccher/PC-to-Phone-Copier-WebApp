
			<!-- Start Content-->
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
						<div class="card">
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
								</div>
								<!-- End Left sidebar -->
								<div class="page-aside-right">
									<div class="d-flex justify-content-between align-items-center">
										<div class="app-search">
											<form>
												<div class="mb-2 position-relative">
													<input type="text" class="form-control" placeholder="Search files...">
													<span class="mdi mdi-magnify search-icon"></span>
												</div>
											</form>
										</div>
										<div></div>
									</div>
									<div class="mt-3">
										<h5 class="mb-2">Quick Access</h5>
										<div class="row mx-n1 g-0 recent_files" id="recent_files">
											<?php echo $file_list; ?>
										</div>
										<!-- end row-->
									</div>
									<!-- end .mt-3-->
									<div class="mt-3">
										<h5 class="mb-3">Recent</h5>
										<div class="table-responsive">
											<table class="table table-centered table-nowrap mb-0">
												<thead class="table-light">
												<tr>
													<th class="border-0">Name</th>
													<th class="border-0">Source</th>
													<th class="border-0">Size</th>
													<th class="border-0">Action</th>
												</tr>
												</thead>
												<tbody class="all_files" id="all_files">
                                                    <?php echo $file_list_all ;?>
												</tbody>
											</table>
										</div>
									</div>
									<!-- end .mt-3-->
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