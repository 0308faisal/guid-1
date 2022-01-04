<?php declare(strict_types=1);
include 'functions.php'; ?>

<?php include 'inc/header.php'; ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

	<?php include 'inc/sidebar.php'; ?>


	<!-- BEGIN PAGE CONTAINER-->
	<div class="page-content">

		<div class="clearfix"></div>
		<div class="content">
			<!-- <ul class="breadcrumb">
			  <li>
				<p>YOU ARE HERE</p>
			  </li>
			  <li><a href="#" class="active">Tables</a> </li>
			</ul> -->
			<div class="page-title">

				<h3>Create new network</h3>

			</div>


			<!--DESCRIPTION EDIT -->
			<div class="row">
				<div class="col-md-8">
					<div class="grid simple">
						<div class="grid-title no-border">
							<h4>Basic information</h4>
							<div class="tools"> <a href="javascript:;" class="collapse"></a></div>
						</div>
						<div class="grid-body no-border">
							<div class="row">
								<div class="col-md-8 col-sm-8 col-xs-8">
									<form name="networkform" id="networkform" method="post" enctype="multipart/form-data" action="<?=SITE_URL; ?>network/networkcreate">
										<input type="hidden" id="token" name="token" value="<?= $_COOKIE['token']; ?>">
										<input type="hidden" id="uid" name="uid" value="<?= $_GET['uid']; ?>">
										<div class="form-group">
											<label class="form-label">Network Name</label>
											<div class="controls">
												<input type="text" name="network_name" class="form-control" placeholder="eg my network">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">Network Description</label>

											<div class="controls">
												<textarea name="network_description" class="form-control">Some info about network</textarea>
											</div>
										</div>

										<div class="form-group">
											<label class="form-label">Country</label>
											<select name="network_country" style="width:100%">

												<option value="103">Ireland</option>

											</select>
										</div>


										<div class="form-group">
											<label class="form-label">Website</label>

											<div class="controls">
												<input name="network_website" type="text" class="form-control" value="www.company.com">
											</div>
										</div>

										<div class="form-group">
											<label class="form-label">Network type</label>
											<select name="network_type" style="width:100%">

												<option value="private">Private network</option>
												<option value="public">Public network</option>

											</select>
										</div>
										<div class="form-group">
											<label class="form-label">Mobile Google Analytics</label>
											<div class="controls">
												<input type="text" class="form-control" placeholder="UA-12345678-1" id="network_ga_m" name="network_ga_m" value="<?= $network['network_ga_m']; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">Web Google Analytics</label>
											<div class="controls">
												<input type="text" class="form-control" placeholder="UA-12345678-1" id="network_ga_w" name="network_ga_w" value="<?= $network['network_ga_w']; ?>">
											</div>
										</div>
										<!--<div class="alert">
											<button class="close" data-dismiss="alert"></button>
											Please note that Private networks are only available on a paid subscription.  Please continue creating your network and a GuideDoc representative will contact you within 1 working day.

											You can also email hello@guidedoc.co with any questions your might have. </div>-->

										<div class="form-group">
											<label class="form-label">Upload Network Logo</label>

											<div class="controls">
												<input name="network_logo" type="file" class="" style="line-height:15px; border:0">
											</div>
										</div>
										<button class="btn btn-primary" style="margin-top:50px">Save Settings</button>
									</form>

								</div>


							</div>
						</div>
					</div>
				</div>



			</div>
		</div>
	</div>
	<!-- END PAGE -->

	<!-- END CONTAINER -->
	<?php include 'inc/scripts.php'; ?>
	<script src="assets/js/pages/network_create.js" type="text/javascript"></script>
	<?php include 'inc/footer.php'; ?>
