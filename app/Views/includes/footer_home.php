
		<!-- content -->
		<!-- Footer Start -->
		<footer class="footer">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-6">
						<script>document.write(new Date().getFullYear())</script> © Niccher Inc
					</div>
					<div class="col-md-6">
						<div class="text-md-end footer-links d-none d-md-block">
							<a href="javascript: void(0);">About</a>
						</div>
					</div>
				</div>
			</div>
		</footer>
		<!-- end Footer -->
		</div>
		<!-- ============================================================== -->
		<!-- End Page content -->
		<!-- ============================================================== -->
		</div>
		<!-- END wrapper -->
		<!-- bundle -->
		<script src="<?php echo base_url('assets/js/vendor.min.js')?>"></script>
		<script src="<?php echo base_url('assets/js/app.min.js')?>"></script>

		<script>
            $(document).ready(function(){
                function getState(){
                    $.ajax({
                        url: "<?php echo base_url('home/get_files_recent');?>",
                        type: 'POST',
                        success: function (response) {
                            $('.recent_files').html(response);
                        },
                        error: function() {
                            //alert('Unknown error');
                        }
                    });

                    $.ajax({
                        url: "<?php echo base_url('home/get_files_all');?>",
                        type: 'POST',
                        success: function (response) {
                            $('.all_files').html(response);
                        },
                        error: function() {
                            //alert('Unknown error');
                        }
                    });
                }
                setInterval(getState, 5000);
            });
		</script>
	</body>
</html>
