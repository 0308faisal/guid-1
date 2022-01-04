<?php include('../functions.php'); ?>
<?php
if(!isset($_GET["passwordtoken"]))
{
	//header("location: /frontend/index.php");
	echo "failed";
}
elseif(isset($_GET["passwordtoken"]) && $_GET["passwordtoken"] != "")
{
	$validpassword = makeRequest("user/validatepassword/?passwordtoken=" . $_GET['passwordtoken'], "POST");
	if($validpassword["status"] == "success")
	{
		$uid = $validpassword['id'];
	}
	else
	{
		echo "<script>alert('" . $validpassword["response"] . "');</script>";
		//header("location: /frontend/register.php");
	}
}
?>
<?php include('inc/reg-header.php') ?>



	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div class="grid simple">
					<div class="grid-title">
						<h3>Change Password</h3>
					</div>
					<form name="guideform" id="guideform" method="post" enctype="multipart/form-data" action="<?=SITE_URL?>user/changepassword">
						<input type="hidden" id="uid" name="uid" value="<?= $uid ?>">
						<input type="hidden" id="passwordtoken" name="passwordtoken" value="<?= $_GET['passwordtoken'] ?>">
						<div class="grid-body">
							<div class="form-group">
								<label class="form-label">Password</label>
								<div class="controls">
									<input class="form-control" type="password" value="" name="password" id="password" placeholder="Enter a password" required>
									<a id='showpass' href='#'>Show Password</a>
								</div>
							</div>
							<input type="submit" id="submitbutton" class="btn btn-primary" value="Save" />
						</div>
				</div>

			</div>
			</form>
		</div>
	</div>







	<!--END PAGE CONTENT-->



	<?php include('inc/scripts.php') ?>
	<script src="assets/js/pages/changepassword.js" type="text/javascript"></script>
	<?php include('inc/footer.php') ?>
