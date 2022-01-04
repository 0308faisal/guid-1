<?php declare(strict_types=1);
include 'functions.php'; ?>
<?php

	$search = isset($_GET['searchterm']) ? '?searchterm=' . $_GET['searchterm'] : '';
	$data = makeRequest('guideline/getguide/' . $_GET['id'], 'GET');

	$guides = makeRequest('guideline/getguides' . $search, 'GET');
	$published_guides = [];

	foreach ($guides['published_guides'] as $key => $array) {
		$published_guides[] = ['id' => $array['id'], 'title' => $array['title']];
	}
	function cmp($a, $b)
	{
		return \strcmp($a['title'], $b['title']);
	}
	\usort($published_guides, 'cmp');

?>
<?php include 'inc/header.php'; ?>
<style type="text/css">
	.text-editor {min-height: 400px;}
	.ui-sorting-highlight { height: 3.5em; line-height: 3.7em; margin-bottom: 1.2em; background-color: #fbf9ee;}
</style>
<div class="page-container row-fluid">
    <?php include 'inc/sidebar.php'; ?>
    <!-- BEGIN PAGE CONTAINER-->
    <div class="page-content">
        <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <div class="modal hide" id="portlet-config">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button"></button>
                <h3>Widget Settings</h3>
            </div>
            <div class="modal-body">
                Widget settings form goes here
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="content">
            <!-- <ul class="breadcrumb">
				<li>
				  <p>YOU ARE HERE</p>
				</li>
				<li><a href="#" class="active">Tables</a> </li>
			  </ul> -->
			<form name="guideform" id="guideform" method="post" enctype="multipart/form-data" action="<?=SITE_URL; ?>guideline/guideedit">
				<input type="hidden" id="alert" value="1">
				<input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
				<input type='hidden' name='id' id='id' value='<?=cleanInputs($_GET['id']); ?>'>
				<input type='hidden' name='activenetwork' id='activenetwork' value='<?=$_SESSION['activenetwork']; ?>'>
				<div class="page-title">
					<h3><?=$data[0]['title']; ?></h3><button class="btn btn-primary" style="float:right" type="button" id="preview">Preview</button>
				</div><!-- BEGIN LEFT COL -->
				<div class="row">
					<div class="col-md-8">
						<div class="grid simple">
							<div class="grid-title no-border">
								<h4>Edit Basic information</h4>
								<div class="tools">
									<a class="collapse" href="javascript:;"></a>
								</div>
							</div>
							<div class="grid-body no-border">
								<div class="row">
									<div class="">
										<div class="form-group">
											<label class="form-label">Guide Name</label>
											<div class="controls">
												<input class="form-control" type="text" id="guidename" name="guidename" value="<?=$data[0]['title']; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">Description</label>
											<div class="controls">
												<textarea class="text-editor form-control" id="guidedescription" name="guidedescription"><?=$data[0]['text']; ?></textarea>
												<select id='selectguidelines' name='selectguidelines'>
												<option value='0'>Add link to another guideline</option>
												<?php
													foreach ($published_guides as $key => $row) {
														echo "<option value='" . $row['id'] . "'>" . $row['title'] . '</option>';
													}
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="form-label">Categories</label> <input class="span12 tagsinput" data-role="tagsinput"
											style="display: none;" type="text" id="guidecategories" name="guidecategories" value="<?=\implode(',', $data[0]['categories']); ?>">
										</div>
										<div class="form-group">
											<label class="form-label">Upload PDF</label>
											<div class="controls">
												<?php if (\is_array($data[0]['files']) && \count($data[0]['files']) > 0) {?>
												<span>Uploaded Files: (select file to delete)</span><br />
												<?php
												}

												foreach ($data[0]['files'] as $key => $item) {
													echo "<input type='checkbox' name='filedelete[]' value='{$item['filename']}'>&nbsp;" . ($item['dl_filename'] !== '' ? $item['dl_filename'] : $item['filename']) . '<br />';
												}
												?>
												<input type="file" class="" style="line-height:15px; border:0" id="guidefile" name="guidefile">
											</div>
										</div>
										<hr />
										<div class="custom-field-container alert alert-error" style="">
											<!--<button class="close" data-dismiss="alert"></button>-->
										<div class="form-group">
											<label class="form-label">Title</label>
											<div class="controls">
												<input type="text" class="form-control" id="guidewarningtitle" name="guidewarningtitle" value="<?=$data[0]['warningtitle'] ?? 'Warning'; ?>" onkeyup="$('#headerspan0').html($(this).val().toLowerCase().replace(/\b[a-z]/g, function(letter) {return letter.toUpperCase();}));">
											</div>
										</div>
										<div class="form-group">
												<label class="form-label">Warning content</label>
												<div class="controls">
													<textarea class="text-editor form-control" id="guidewarning" name="guidewarning"><?=$data[0]['warning']; ?></textarea>
												</div>
										</div>
										</div>
										<h4>Citation References</h4>
										<?php if (!isset($data[0]['citations']) || \count($data[0]['citations']) == 0) { ?>

											<div class="citation-wrapper">
												<div class="input-group">
													<span class="input-group-addon"></span>
													<input type="text" class="form-control" placeholder="Author" name="citationauthor[]" value="">
												</div>
												<br />
												<div class="input-group">
													<span class="input-group-addon"></span>
													<input type="text" class="form-control" placeholder="Citation Reference" name="citationreference[]" value="">
												</div>
												<br />
											</div>

										<?php } else { ?>
											<?php foreach ($data[0]['citations'] as $key => $value) {
													if ($value['parent_guideline_id'] > 0) {?>
													<div class="citation-wrapper">
														<input type="hidden" name="citationparentguidelineid[]" value="<?=$value['parent_guideline_id']; ?>">
														<div class="input-group">
															<span class="input-group-addon"></span>
															<input type="text" class="form-control" placeholder="Network" name="citationnetwork[]" value="<?=$value['network']; ?>" readonly>
														</div>
														<br>
														<div class="input-group">
															<span class="input-group-addon"></span>
															<input type="text" class="form-control" placeholder="Author" name="citationauthor[]" value="<?=$value['author']; ?>" readonly>
														</div>
														<br />
														<div class="input-group">
															<span class="input-group-addon"></span>
															<input type="text" class="form-control" placeholder="Citation Reference" name="citationreference[]" value="<?=$value['reference']; ?>" readonly>
														</div>
														<br />
													</div>
												<?php
												} else {?>
												<div class="citation-wrapper">
													<div class="input-group">
														<span class="input-group-addon"></span>
														<input type="text" class="form-control" placeholder="Author" name="citationauthor[]" value="<?=$value['author']; ?>">
													</div>
													<br />
													<div class="input-group">
														<span class="input-group-addon"></span>
														<input type="text" class="form-control" placeholder="Citation Reference" name="citationreference[]" value="<?=$value['reference']; ?>">
													</div>
													<br />
												</div>
											<?php } ?>

											<?php
												} ?>
										<?php } ?>
										<button class="btn btn-primary" id="repeat-citation-btn" type="button">Add Citation</button>
										<br />
										<hr />
										<input class="btn btn-primary savebutton" type="submit" value="Save Changes">
									</div>
								</div>
							</div>
						</div>
						<div class="grid simple">
							<div class="grid-title no-border">
								<h4>Custom Content</h4>
								<div class="tools">
									<a class="collapse" href="javascript:;"></a>
								</div>
							</div>
							<div class="grid-body no-border">
								<div class="row">
									<div id="content-holder">

										<?php if (!isset($data[0]['contents'])) { ?>

												<div class="content-wrapper">
												<div class="custom-field-container alert alert-success" style="">
													<a class="expandlink" data-id="0" href="#">+</a>&nbsp;<span id="headerspan0"></span><button class="close" onclick="if (confirm('You are about to delete this content, please click okay to confirm') == true) {var editor = $('#editor0').ckeditorGet();CKEDITOR.remove(editor);$(this).closest('.content-wrapper').remove();$(this).parent().alert('close');}return false;"></button>
													<div id='contents0'>
														<div class="form-group">
															<label class="form-label">Title</label>
															<div class="controls">
																<input type="text" class="form-control"  name="customcontenttitle[]" value="" onkeyup="$('#headerspan0').html($(this).val().toLowerCase().replace(/\b[a-z]/g, function(letter) {return letter.toUpperCase();}));">
															</div>
														</div>
														<div class="form-group">
															<label class="form-label">Content</label>
															<div class="controls">
																<textarea class="text-editor form-control" id="editor0" name="customcontent[]"></textarea>
																<select class='dinamicguidelineselector' data-id="0">
																<option value='0'>Add link to another guideline</option>
																<?php
																	foreach ($published_guides as $key => $row) {
																		echo "<option value='" . $row['id'] . "'>" . $row['title'] . '</option>';
																	}
																?>
																</select>
															</div>
														</div>
													</div>
												</div>
												<br />
											</div>

										<?php } else { ?>
											<?php foreach ($data[0]['contents'] as $key => $value) { ?>

											<div class="content-wrapper">
												<div class="custom-field-container alert alert-success" style="">
													<a class="expandlink" data-id="<?=$key; ?>" href="#">+</a>&nbsp;<span id="headerspan<?=$key; ?>"><?=\ucwords($value['title']); ?></span><button class="close"  onclick="if (confirm('You are about to delete this content, please click okay to confirm') == true) {var editor = $('#editor<?=$key; ?>').ckeditorGet();CKEDITOR.remove(editor);$(this).closest('.content-wrapper').remove();$(this).parent().alert('close');}return false;"></button>
													<div id='contents<?=$key; ?>' style='display:none;'>
														<div class="form-group">
															<label class="form-label">Title</label>
															<div class="controls">
																<input type="text" class="form-control" name="customcontenttitle[]" value="<?=$value['title']; ?>"  onkeyup="$('#headerspan<?=$key; ?>').html($(this).val().toLowerCase().replace(/\b[a-z]/g, function(letter) {return letter.toUpperCase();}));">
															</div>
														</div>
														<div class="form-group">
															<label class="form-label">Content</label>
															<div class="controls">
																<textarea class="text-editor form-control" id="editor<?=$key; ?>" name="customcontent[]"><?=$value['content']; ?></textarea>
																<select class='dinamicguidelineselector' data-id="<?=$key; ?>">
																<option value='0'>Add link to another guideline</option>
																<?php
																	foreach ($published_guides as $key => $row) {
																		echo "<option value='" . $row['id'] . "'>" . $row['title'] . '</option>';
																	}
																?>
																</select>
															</div>
														</div>
													</div>
												</div>
												<br />
											</div>

											<?php } ?>
										<?php } ?>
										<button class="btn btn-primary" id="repeat-content-btn">Add Content</button>
										<br /><br />
										<hr />
										<input class="btn btn-primary savebutton" type="submit" value="Save Changes">
									</div>
								</div>
							</div>
						</div>
					</div><!-- END LEFT COL -->
					<!-- BEGIN RIGHT COL -->
					<div class="col-md-4">
					<div class="grid simple" style="width:500px !important;">
						<div class="grid-title no-border">
							<h4>Publish</h4>
							<div class="tools">
								<a class="collapse" href="javascript:;"></a>
							</div>
						</div>
						<div class="grid-body no-border">
							<!--<div class="row-fluid">
								<p style="font-size:14px; color:#333">Status: <b> Draft </b> <a href="#" style="text-decoration:underline">Edit</a></p>
							</div>-->
							<div class="clear"></div>
							<div class="row-fluid">
								<div class="checkbox check-default">
									<input type="checkbox" value="1" id="publish" name="publish" <?=($data[0]['publish'] == '1' ? 'checked' : ''); ?>> <label for="publish">Publish on your network</label>
								</div>
							</div>
							<div class="row-fluid">
								<div class="form-group">
									<p style="font-size:14px; color:#333"><b>Change Log Note (Required):</b></p>
									<textarea name="logcomment" id="logcomment" placeholder="Please document your change here..." style="width: 332px;height: 89px;"></textarea>
							</div>

								<div class="row-fluid">
								<div class="checkbox check-default" style="width:330px">
									<input id="terms" type="checkbox" value="1" class="terms" name="terms"> <label for="terms">By selecting this checkbox - you have completed all necessary testing and due diligence on this guideline and accept the GuideDoc terms and conditions.</label>
								</div>
							</div>
								<input class="btn btn-primary savebutton" type="submit" value="Save Changes">
							</div>
							<h4>User Comments</h4>
							<div class="comments-section popover-content" style="border-top:2px solid;">
								<?php foreach ($data[0]['comments'] as $key => $item) {?>
							<div class="notification-messages white">
									<div class="user-profile">
										<img src="../assets/img/profiles/bc.jpg" alt="" data-src="assets/img/profiles/bc.jpg" data-src-retina="assets/img/profiles/bc2x.jpg" width="35" height="35">
									</div>
									<div class="message-wrapper">
										<div class="heading">
											<?=$item['member']; ?>
										</div>
										<div class="description">
											<?=$item['comment']; ?>
										</div>
										<div class="date pull-left">
											<?=$item['cdate']; ?>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<?php }?>
							</div>
							<h4>Change History</h4>
							<div class="comments-section popover-content" style="border-top:2px solid;">
								<?php foreach ($data[0]['history'] as $key => $item) {?>
								<div class="notification-messages white">
									<div class="message-wrapper" style="width:330px">
										<div>
											<strong>Date and Time:</strong> <?=$item['mdate']; ?><br />
											<strong>Change by:</strong> <?=$item['email']; ?></br />
											<strong>Note:</strong> <?=$item['comment']; ?>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<?php }?>
							</div>
						</div>
					</div><!-- END RIGHT COL -->
				</div><!--END ROW-->
			</form>
		</div><!-- END PAGE -->
	</div><!-- END CONTAINER -->
</div>
<div id="myModal" class="modal fade">
    <div class="modal-dialog" style="width:990px;height:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="modalclose" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Popup</h4>
            </div>
            <div class="modal-body">
               <iframe id="iframe" style="width:960px;height:800px;"></iframe>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<?php include 'inc/scripts.php'; ?>
<script src="assets/js/pages/guide_edit.js" type="text/javascript"></script>
<?php include 'inc/footer.php'; ?>
