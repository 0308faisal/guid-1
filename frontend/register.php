<?php include('../functions.php') ?>
<?php
if ($user) {
	header("location: /frontend/index.php");
}

if (isset($_POST["email"]) && $_POST["email"] != "" && isset($_POST["password"]) && $_POST["password"] != "") {
	$data = makeRequest("auth/authenticate/?email=" . $_POST["email"] . "&password=" . $_POST["password"], "POST");
	if ($data["status"] == "success") {
		$_SESSION["token"] = $data["token"];
		header("location: index.php?login=true");
	} else {
		echo "<script>alert('" . $data["response"]. "');</script>";
	}
}
if (isset($_GET['activate']) && $_GET['activate'] == "1" && isset($_GET['nid']) && $_GET['nid'] != "" && isset($_GET['uid']) && $_GET['uid'] != "") {
	$validemail = makeRequest("user/validateemail/?nid=" . $_GET['nid'] . "&uid=" . $_GET['uid'], "POST");
	if ($validemail["status"] == "success") {
		echo "<script>alert('" . $validemail['response'] . "');</script>";
	} elseif ($validemail["status"] == "pending") {
		header("location: pending.php?nid=" . $validemail['nid']);
	} else {
		echo "<script>alert('" . $validemail["response"] . "');</script>";
	}
}
if (isset($_GET['nid']) && $_GET['nid'] != "" && isset($_GET['uid']) && $_GET['uid'] != "") {
	$validinvite = makeRequest("user/validateinvite/?nid=" . $_GET['nid'] . "&uid=" . $_GET['uid'], "POST");
	if ($validinvite["status"] == "success") {
		$nid = $validinvite['nid'];
		$uid = $validinvite['uid'];
	}
} else {
	$nid = "";
	$uid = "";
}
$network = makeRequest("network/getnetworks", "GET");
$profession = makeRequest("guideapi/getdropdowndata?field=profession", "GET");
$grade = makeRequest("guideapi/getdropdowndata?field=grade", "GET");

?>
<?php include('inc/reg-header.php') ?>

<!--PAGE CONTENT-->

<div class="container-fluid">
	<div class="page-title">
		<h3>Login or Register for GuideDoc</h3>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="grid simple">
				<div class="grid-title">
					<h4>Register</h4>
				</div>
				<form name="guideform" id="guideform" method="post" enctype="multipart/form-data" action="<?= SITE_URL ?>user/register">
					<input type="hidden" id="nid" name="nid" value="<?= $nid ?>">
					<input type="hidden" id="uid" name="uid" value="<?= $uid ?>">
					<input type="hidden" id="community" name="community" value="<?= isset($_GET['community']) ? 'true' : 'false' ?>">
					<div class="grid-body">
						<div class="form-group">
							<label class="form-label">Firstname</label>
							<div class="controls">
								<input class="form-control" type="text" value="" name="firstname" id="firstname" placeholder="firstname" required>

							</div>
						</div>
						<div class="form-group">
							<label class="form-label">Lastname</label>
							<div class="controls">
								<input class="form-control" type="text" value="" name="lastname" id="lastname" placeholder="lastname" required>

							</div>
						</div>
						<div class="form-group">
							<label class="form-label">Email</label>
							<div class="controls">
								<input class="form-control" type="text" value="" name="email" id="email" placeholder="Email" required>

							</div>
						</div>

						<div class="form-group">
							<label class="form-label">Password</label>
							<div class="controls">
								<input class="form-control" type="password" value="" name="password" id="password" autocomplete="off" placeholder="Enter a password" required>
								<a id='showpass' href='#'>Show Password</a>
							</div>
						</div>
						<?php if (!isset($_GET['community'])) { ?>
							<div class="form-group">
								<label class="form-label">Profession</label>
								<div class="controls">
									<select class="form-control" name="occupation" id="occupation" required>
										<option>Please Select</option>
										<?php
										foreach ($profession["data"] as $key => $value) {
											echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
										}
										?>
									</select>

								</div>
							</div>

							<div class="form-group" id="gradesection" style="display:none">
								<label class="form-label">Grade</label>
								<div class="controls">
									<select class="form-control" name="grade" id="grade">
										<option>Please Select</option>
										<?php
										//foreach($grade[0] as $key=>$value){
										//	echo "<option value='".$value['id']."'>".$value['name']."</option>";
										//}
										?>
									</select>

								</div>
							</div>
							<div class="form-group">
								<label class="form-label">Network</label>
								<div class="controls">
									<select class="form-control" name="network" id="network" required>
										<option value="">None</option>
										<?php
										foreach ($network as $key => $value) {
											if ($value['network_type'] != "hidden") {
												echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
											}
										}
										?>
									</select>

								</div>
							</div>

							<div class="form-group">
								<div class="checkbox check-primary">
									<input id="checkbox3" name="checkbox3" type="checkbox" value="1">
									<label for="checkbox3">I am a healthcare professional</label>
								</div>
							</div>
						<?php } ?> 
						<?php if ($ignore_captcha == false) { ?>
							<div class="g-recaptcha" data-sitekey="<?= HOSTS[$_SERVER['SERVER_NAME']]['captcha_key'] ?>"></div>
						<?php } ?>
						<input type="submit" id="submitbutton" class="btn btn-primary" value="Register" />
					</div>
			</div>

		</div>
		</form>
	</div>




</div>







<!--END PAGE CONTENT-->



<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/register.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>