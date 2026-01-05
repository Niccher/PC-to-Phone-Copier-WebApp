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
			<ul class="side-nav">
				<li class="side-nav-title side-nav-item">Home</li>

				<?php
				    echo "<li class='side-nav-item'>";
				    $url_home = base_url('home');

                    if ( is_in_dashboard($title)) {
	                    echo '<a href="'.$url_home.'" class="side-nav-link fw-bolder text-white">';
                    }else{
	                    echo '<a href="'.$url_home.'" class="side-nav-link fw-bolder text-muted">';
                    }
                    $url_home = base_url('home');
                    echo '
                            <i class="uil-home"></i>
                            <span> Dash </span>
                        </a>
                    </li>';
				?>
				<li class="side-nav-title side-nav-item">Others</li>
				<li class="side-nav-item">
					<a href="<?php echo base_url('auth/logout')?>" class="side-nav-link">
						<i class="uil-comments-alt"></i>
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