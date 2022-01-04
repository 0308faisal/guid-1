<?php declare(strict_types=1);
include 'functions.php'; ?>
<?php
	$networkinfo = makeRequest('network/getnetwork/' . $_GET['id'], 'GET');
	$networkid = \is_numeric($_GET['id']) ? $_GET['id'] : 0;
	$networklist = makeRequest('network/getnetworks', 'GET');
	$country = makeRequest('guideapi/getdropdowndata?field=country', 'GET');
?>
<?php include 'inc/header.php'; ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include 'inc/sidebar.php'; ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <!-- <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="#" class="active">Tables</a> </li>
      </ul> -->
      <div class="page-title">

          <h3>Manage Network</h3>

      </div>

      <div class="row">
      <div class="col-md-12">
          <ul class="nav nav-tabs" id="tab-01">
            <li class="active"><a href="#invitemembers">Invite Members</a></li>
            <li class=""><a href="#guidelines">Guidelines</a></li>
            <li class=""><a href="#settings">Network Settings</a></li>
            <li class=""><a href="#invitemanager">Invite Manager</a></li>

          </ul>
          <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
          <div class="tab-content">
            <div class="tab-pane active" id="invitemembers">
              <h3>Invite Members</h3>
              <p>Send invitations to your connections on GuideDoc or directly by adding their email address below. Recipients who accept your invitations will automatically become members of your Network.
              <div class="row">
                <div class="col-md-8 col-sm-8 col-xs-8">
                  <form name="inviteform" id="inviteform" method="post" enctype="multipart/form-data" action="<?=SITE_URL; ?>network/sendinvites/<?=$networkid; ?>">
                    <input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
                    <input type="hidden" id="networkid" name="networkid" value="<?=$networkid; ?>">
                    <div class="form-group">
                      <label class="form-label">Add email addresses of contacts not on GuideDoc:(Separate each email address with a return)</label>
                      <div class="controls">
                        <input class="span12 tagsinput" data-role="tagsinput"style="display: none;" type="text" id="emails" name="emails">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Subject</label>
                      <div class="controls">
                        <input type="text" id="invitationsubject" name="invitationsubject" class="form-control" placeholder="Invitation subject here">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Body</label>
                      <div class="controls">
                        <textarea class="form-control" id="invitationtext" name="invitationtext">Invitation text here.</textarea>
                      </div>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Invite">
                  </form>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="guidelines">
              <div class="row">
                <div class="col-md-12">
                  <?php include 'modules/guidetable.php'; ?>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="settings">

              <div class="row">
                <div class="col-md-8">
                  <h3>Network Settings</h3>
                  <form name="networkform" id="networkform" method="post" enctype="multipart/form-data" action="<?=SITE_URL; ?>network/networkedit/<?=$networkid; ?>">
                    <input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
                    <div class="form-group">
                      <label class="form-label">Network Name</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="eg my network" id="network_name" name="network_name" value="<?=$networkinfo['network_name']; ?>">
                      </div>
                    </div>
					<div class="form-group">
                      <label class="form-label">Domain</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="eg mydomain.com" id="network_domain" name="network_domain" value="<?=$networkinfo['network_domain']; ?>"><input type="checkbox" id="network_approve" name="network_approve" <?=$networkinfo['network_approve'] == '1' ? 'checked' : ''; ?>>Auto-accept users from same domain
                      </div>
                    </div>
					<div class="form-group">
                      <label class="form-label">Clone From</label>
                      <div class="controls">
                        <select class="form-control" name="clonefrom" id="clonefrom">
							<option value='0'>None</option>
							<?php
							foreach ($networklist['networks'] as $key => $value) {
								$selected = $value['id'] == $networkinfo['clonefrom'] ? 'selected' : '';

								if ($value['network_type'] !== 'hidden' && $value['id'] !== $networkinfo['id']) {
									echo "<option value='" . $value['id'] . "' ${selected}>" . $value['name'] . '</option>';
								}
							}
							?>
						</select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Network Description</label>
                      <div class="controls">
                        <textarea class="form-control" id="network_description" name="network_description"><?=$networkinfo['description']; ?></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Upload Network Logo</label>
                      <div class="controls">
                        <input type="file" class=""  id="network_logo" name="network_logo" style="line-height:15px; border:0">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Country</label>
                      <select id="network_country" name="network_country" style="width:100%">
                        <?php
						foreach ($country['data'] as $key => $value) {
							echo "<option value='" . $value['id'] . "' " . ($value['name'] == 'IRELAND' ? 'selected' : '') . '>' . $value['name'] . '</option>';
						}
						?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Network owner email</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="me@company.com" id="network_email" name="network_email" value="<?=$networkinfo['owner_email']; ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Website</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="www.company.com" id="network_website" name="network_website" value="<?=$networkinfo['network_website']; ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Network Type</label>
                      <select id="network_type" name="network_type" style="width:100%">
                        <option value="public" <?=($networkinfo['network_type'] == 'public' ? 'selected' : ''); ?>>Public</option>
                        <option value="private"<?=($networkinfo['network_type'] == 'private' ? 'selected' : ''); ?>>Private</option>
                        <option value="hidden"<?=($networkinfo['network_type'] == 'hidden' ? 'selected' : ''); ?>>Hidden</option>
                      </select>
                    </div>
										<div class="form-group">
                      <label class="form-label">Mobile Google Analytics</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="UA-12345678-1" id="network_ga_m" name="network_ga_m" value="<?=$networkinfo['network_ga_m']; ?>">
                      </div>
                    </div>
										<div class="form-group">
                      <label class="form-label">Web Google Analytics</label>
                      <div class="controls">
                        <input type="text" class="form-control" placeholder="UA-12345678-1" id="network_ga_w" name="network_ga_w" value="<?=$networkinfo['network_ga_w']; ?>">
                      </div>
                    </div>
                    <!--<div class="form-group">
                    <label class="form-label">Network type</label>
                    <select id="network-type" style="width:100%" class="form-control">
                    <option>Please select...</option>
                    <option value="Private">New private network</option>
                    <option value="Peer">Peer review network</option>
                    <option value="Public">Public network</option>
                    </select>
                    </div>-->
                    <div id="if_private_network" class="hidden">
                      <!--<div class="alert">
                        <button class="close" data-dismiss="alert"></button>
                        Please note that Private networks are only available on a paid subscription.  Please continue creating your network and a GuideDoc representative will contact you within 1 working day.
                        You can also email hello@guidedoc.co with any questions your might have.
                      </div>-->
                      <h3>Advanced Settings</h3>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label class="form-label">Add specific registration questions</label>
                            <div class="controls">
                              <input type="text" class="form-control" placeholder="Enter your registration field name">
                              <br>
                              <select class="form-control">
                                <option>
                                  Field Type
                                </option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <button class="btn btn-primary" style="margin-top:50px">Add Field</button>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label class="form-label">Add associated ward names</label>
                            <div class="controls">
                              <textarea class="form-control">One per line...</textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <button class="btn btn-primary" style="margin-top:50px">Add Wards</button>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label class="form-label">Add disclaimer</label>
                            <div class="controls">
                              <textarea class="form-control text-editor"></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <button class="btn btn-primary" style="margin-top:50px">Add disclaimer</button>
                        </div>
                      </div>
                    </div>
                    <button class="btn btn-primary" style="margin-top:50px">Save Settings</button>
                  </form>
                </div>
								<div>
									<img style="max-width:300px" src="/frontend/download.php?file=<?=$networkinfo['network_logo'][0]['filename']; ?>&filename=<?=$networkinfo['network_logo'][0]['dl_filename']; ?>">
								</div>
              </div>
            </div>
            <div class="tab-pane" id="invitemanager">
              <div class="row">
                <div class="col-md-12">
				          <?php include 'modules/invitetable.php'; ?>
                </div>
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
<script src="assets/js/pages/network_edit.js" type="text/javascript"></script>
<?php include 'inc/footer.php'; ?>
