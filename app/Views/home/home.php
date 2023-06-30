
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
										<div class="row mx-n1 g-0">
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-folder-zip font-16"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">Hyper-sketch.zip</a>
																<p class="mb-0 font-13">2.3 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-folder font-16"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">Compile Version</a>
																<p class="mb-0 font-13">87.2 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-primary-lighten text-primary rounded">
																			<i class="mdi mdi-folder-zip-outline font-16"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">admin.zip</a>
																<p class="mb-0 font-13">45.1 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-file-pdf-outline font-16"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">Docs.pdf</a>
																<p class="mb-0 font-13">7.5 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-file-pdf-outline font-18"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">License-details.pdf</a>
																<p class="mb-0 font-13">784 KB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-folder-account font-18"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">Purchase Verification</a>
																<p class="mb-0 font-13">2.2 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
											<div class="col-xxl-3 col-lg-6">
												<div class="card m-1 shadow-none border">
													<div class="p-2">
														<div class="row align-items-center">
															<div class="col-auto">
																<div class="avatar-sm">
																			<span class="avatar-title bg-light text-secondary rounded">
																			<i class="mdi mdi-folder-account font-18"></i>
																			</span>
																</div>
															</div>
															<div class="col ps-0">
																<a href="javascript:void(0);" class="text-muted fw-bold">Hyper Integrations</a>
																<p class="mb-0 font-13">874 MB</p>
															</div>
														</div>
														<!-- end row -->
													</div>
													<!-- end .p-2-->
												</div>
												<!-- end col -->
											</div>
											<!-- end col-->
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
													<th class="border-0">Size</th>
													<th class="border-0">Action</th>
												</tr>
												</thead>
												<tbody>
                                                    <tr>
                                                        <td>
                                                            <span class="fw-semibold">
                                                                <a href="javascript: void(0);" class="text-reset">App Design & Development</a>
                                                            </span>
                                                            <br>
                                                            <span class="font-12">Date()</span>
                                                        </td>
                                                        <td>128 MB</td>
                                                        <td class="">
                                                            <a href="#"><i class="mdi mdi-link widget-icon"></i></a>
                                                            <a href="#"><i class="mdi mdi-download widget-icon"></i></a>
                                                            <a href="#"><i class="mdi mdi-trash-can widget-icon"></i></a>
                                                        </td>
                                                    </tr>
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