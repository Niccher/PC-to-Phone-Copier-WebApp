<?php

    function is_in_dashboard($passed){
        $dash_pages = array("file", "text", "recent");
        if (in_array($passed, $dash_pages)){
            return true;
        }else{
            return false;
        }
    }
?>

<style>
/* Sidebar Active State Enhancements */
.side-nav-link.active {
    background: rgba(114, 124, 245, 0.15) !important;
    border-radius: 50px !important;
    color: #727cf5 !important;
    font-weight: 700 !important;
    margin: 0 10px;
}
.side-nav-link.active i {
    color: #727cf5 !important;
}
.side-nav-link:hover {
    border-radius: 8px !important;
    margin: 0 10px;
}
.side-nav-item {
    margin-bottom: 4px;
}
</style>

<!-- Begin page -->
<div class="wrapper">
	<!-- ========== Left Sidebar Start ========== -->
	<div class="leftside-menu">
		<!-- LOGO -->
		<a href="<?php echo base_url('home')?>" class="logo text-center logo-light">
				<span class="logo-lg">
				<img src="<?php echo base_url('assets/images/logo.png')?>" alt="" height="16">
				</span>
			<span class="logo-sm">
				<img src="<?php echo base_url('assets/images/logo_sm.png')?>" alt="" height="16">
				</span>
		</a>
		<!-- LOGO -->
		<a href="<?php echo base_url('home')?>" class="logo text-center logo-dark">
				<span class="logo-lg">
				<img src="<?php echo base_url('assets/images/logo-dark.png')?>" alt="" height="16">
				</span>
			<span class="logo-sm">
				<img src="<?php echo base_url('assets/images/logo_sm_dark.png')?>" alt="" height="16">
				</span>
		</a>
		<div class="h-100" id="leftside-menu-container" data-simplebar>
			<!--- Sidemenu -->
			<ul class="side-nav mt-2">
				<li class="side-nav-title side-nav-item">Navigation</li>

				<li class="side-nav-item">
					<a href="<?php echo base_url('home/recent'); ?>" class="side-nav-link <?php echo ($title == 'recent') ? 'active' : ''; ?>">
						<i class="uil-history"></i>
						<span> Recent </span>
						<span class="badge rounded-pill bg-info float-end"><?php echo $recent_count ?? 0; ?></span>
					</a>
				</li>

				<li class="side-nav-item">
					<a href="<?php echo base_url('home/files'); ?>" class="side-nav-link <?php echo ($title == 'files') ? 'active' : ''; ?>">
						<i class="uil-file-upload"></i>
						<span> My Files </span>
						<span class="badge rounded-pill bg-primary float-end"><?php echo $files_count ?? 0; ?></span>
					</a>
				</li>

				<li class="side-nav-item">
					<a href="<?php echo base_url('home/texts'); ?>" class="side-nav-link <?php echo ($title == 'text') ? 'active' : ''; ?>">
						<i class="uil-text-fields"></i>
						<span> Text Data </span>
						<span class="badge rounded-pill bg-success float-end"><?php echo $texts_count ?? 0; ?></span>
					</a>
				</li>

				<li class="side-nav-item">
					<a href="<?php echo base_url('home/trashed'); ?>" class="side-nav-link <?php echo ($title == 'trash') ? 'active' : ''; ?>">
						<i class="uil-trash-alt"></i>
						<span> Trash </span>
						<span class="badge rounded-pill bg-danger float-end"><?php echo $trash_count ?? 0; ?></span>
					</a>
				</li>

                <li class="side-nav-title side-nav-item mt-3">Storage</li>
                <li class="side-nav-item px-3 mb-2">
                    <?php
                        $mod_upload_quota = new \App\Models\ModUpload();
                        $sess_id = session()->get('sess_id');
                        if ($sess_id) {
                            $used_quota = $mod_upload_quota->get_session_storage_used($sess_id);
                            $max_quota = 500 * 1024 * 1024; // 500MB
                            $quota_percent = min(100, ($used_quota / $max_quota) * 100);
                            $quota_color = $quota_percent > 90 ? 'bg-danger' : ($quota_percent > 75 ? 'bg-warning' : 'bg-success');
                    ?>
                    <div class="quota-container text-center p-2 rounded border border-secondary bg-light-lighten">
                        <h6 class="text-uppercase font-10 fw-bold text-white mb-2"><i class="mdi mdi-harddisk me-1"></i>Storage Quota</h6>
                        <div class="progress mb-1 shadow-sm" style="height: 5px;">
                            <div class="progress-bar <?php echo $quota_color; ?>" role="progressbar" style="width: <?php echo $quota_percent; ?>%"></div>
                        </div>
                        <p class="text-white fw-bold font-16 mb-0"><?php echo $mod_upload_quota->bytes_to_human_filesize($used_quota); ?> / 500.00 MB</p>
                    </div>
                    <?php } ?>
                </li>

                <li class="side-nav-title side-nav-item mt-2">Pairing</li>
                <li class="side-nav-item px-3 mb-3">
                    <div class="pair-phone-section text-center p-2 rounded border border-secondary bg-light-lighten">
                        <h6 class="text-uppercase font-10 fw-bold text-muted mb-2"><i class="mdi mdi-cellphone-link me-1"></i>Pair Phone</h6>
                        <div class="qr-wrapper p-1 bg-white rounded shadow-sm d-inline-block mb-1">
                            <div id="pair-qr"></div>
                        </div>
                        <p class="text-muted font-10 mb-0">Scan to open on mobile</p>
                    </div>
                </li>

				<li class="side-nav-title side-nav-item mt-2">System</li>
                <li class="side-nav-item" id="pwa-install-item" style="display: none;">
                    <a href="javascript:void(0);" onclick="window.triggerPWAInstall && window.triggerPWAInstall()" class="side-nav-link text-success fw-bold">
                        <i class="mdi mdi-cellphone-arrow-down"></i>
                        <span> Install Web App </span>
                    </a>
                </li>
				<li class="side-nav-item">
					<a href="<?php echo base_url('auth/logout')?>" class="side-nav-link">
						<i class="uil-exit"></i>
						<span> Logout </span>
					</a>
				</li>
			</ul>
            <!-- End Sidebar -->
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -left -->
    </div>
    <!-- Left Sidebar End -->
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <div class="content">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <ul class="list-unstyled topbar-menu float-end mb-0">
                    <li class="dropdown notification-list d-lg-none">
                        <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="dripicons-search noti-icon"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                            <form class="p-3">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                            </form>
                        </div>
                    </li>

                    <li class="notification-list">
                        <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="account-user-avatar">
                                <img src="<?php echo base_url('assets/images/barcode.png')?>" alt="user-image" class="rounded-circle">
                            </span>
                            <span>
                                <span class="account-user-name">More actions</span>
                                <span class="account-position">info</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="mdi mdi-lock-outline me-1"></i>
                                <span>Lock Screen</span>
                            </a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="mdi mdi-logout me-1"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="button-menu-mobile open-left">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>
            <!-- end Topbar -->