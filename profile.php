<?php include('functions.php') ?>
<?php
	if(isset($_GET['searchterm'])){
		$search=isset($_GET['searchterm'])?"&searchterm=".$_GET['searchterm']:"";
		$_SESSION['searchterm']=$_GET['searchterm'];
	}
	else{
		unset($_SESSION['searchterm']);
	}
?>

<?php include('inc/header.php') ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include('inc/sidebar.php') ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="container-fluid">
      <div class="page-title">

          <h3>My Profile</h3>

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
                    <div class="col-md-10">
                      <form name="profileform" id="profileform" method="post" enctype="multipart/form-data" action="<?=SITE_URL?>user/memberprofile">
                      <input type="hidden" name="userid" value="<?=$GLOBALS["user"]->id?>">
                      <input type="hidden" name="token" value="<?=$_SESSION["token"]?>">
                      <div class="form-group">
                        <label class="form-label">First Name</label>
                        <div class="controls">
                          <input type="text" class="form-control" name="firstname" value="<?=$GLOBALS["user"]->firstname?>">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <div class="controls">
                          <input type="text" class="form-control" name="lastname" value="<?=$GLOBALS["user"]->lastname?>">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Occupation</label>
                          <select class="form-control" name="occupation" id="occupation">
								<option></option>
				            	<?php
									foreach($profession[0] as $key=>$value){
										$selected=($GLOBALS["user"]->occupation==$value['name']?"selected":"");
										echo "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
									}
				            	?>
				            </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Speciality</label>
                          <select class="form-control" name="speciality" id="speciality">
								<option></option>
				            	<?php
									foreach($speciality[0] as $key=>$value){
										$selected=($GLOBALS["user"]->specialty==$value['name']?"selected":"");
										echo "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
									}
				            	?>
				            </select>
                      </div>
                      <div class="form-group">
                        <label class="form-label">Employer</label>
                        <div class="controls">
                          <input type="text" class="form-control" name="employer" value="<?=$GLOBALS["user"]->employer?>">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="form-label">Gradename</label>
                        <div class="controls">
                          <select class="form-control" name="gradename" id="gradename">
								<option></option>
				          		<?php
									foreach($grade[0] as $key=>$value){
										$selected=($GLOBALS["user"]->gradename==$value['name']?"selected":"");
										echo "<option value='".$value['id']."' ".$selected.">".$value['name']."</option>";
									}
				            	?>
				            </select>
                        </div>
                      </div>

                      <div class="form-group">
                       <label class="form-label">Country</label>
                        <select style="width:100%" name="country">
							<?php
									foreach($country[0] as $key=>$value){
										echo "<option value='".$value['id']."' ".($value['name']=="IRELAND"?"selected":"").">".$value['name']."</option>";
									}
				            	?>

                      </select>
                      </div>

                      <!--<div class="form-group">
                          <label class="form-label">Upload Profile Image</label>

                          <div class="controls">
                              <input type="file" name="avatar" class="" style="line-height:15px; border:0">
                          </div>
                      </div>-->

                      <input type="submit" class="btn btn-primary" value="Update" />
					  </form>
                    </div>

                  </div>
                </div>
              </div>
            </div>
      </div>



     <div class="row">
            <div class="col-md-8">
              <div class="grid simple">
                <div class="grid-title no-border">
                  <h4>Change Password</h4>
                </div>
                <div class="grid-body no-border">
				<form name="passwordform" id="passwordform" method="post" enctype="multipart/form-data" action="<?=SITE_URL?>user/memberpassword">
                 <input type="hidden" name="userid" value="<?=$GLOBALS["user"]->id?>">
                 <input type="hidden" name="token" value="<?=$_SESSION["token"]?>">
                 <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <span class="help">Enter your current password</span>
                    <div class="controls">
						<input type="password" name="oldpassword" autocomplete="off" class="form-control">
                    </div>
                </div>

                 <div class="form-group">
                    <label class="form-label">New Password</label>
                    <span class="help">Passwords must contain a capital letter, at least one number and have at least 6 characters in length.
     </span>
                    <div class="controls">
						<input type="password" name="newpassword" autocomplete="off" class="form-control tip" data-toggle="tooltip" data-original-title="Loads of tooltips!!">

                    </div>
                </div>

                 <div class="form-group">
                    <label class="form-label">New Password Again</label>
                    <div class="controls">
						<input type="password" name="newpasswordconfirm" autocomplete="off" class="form-control">
                    </div>
                </div>



                <input type="submit" class="btn btn-primary" value="Change" />
                </form>
                </div>
              </div>
            </div>


      </div>


  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/profile.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
