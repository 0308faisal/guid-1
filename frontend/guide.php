<?php declare(strict_types=1);
include '../functions.php'; ?>
<?php
	$data = makeRequest('guideline/getguide/' . $_GET['id'], 'GET');
?>
<?php
	if (isset($_GET['searchterm'])) {
		$search = isset($_GET['searchterm']) ? '&searchterm=' . $_GET['searchterm'] : '';
		$_SESSION['searchterm'] = $_GET['searchterm'];
	} else {
		unset($_SESSION['searchterm']);
	}
?>
<?php include 'inc/header.php'; ?>
<input type="hidden" id="network_ga" value="<?=$data[0]['network_ga']; ?>">

	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="page-title">
        	<h3><?=\preg_replace('/' . $_SESSION['searchterm'] . '/i', "<span class='highlight'>\$0</span>", $data[0]['title']); ?></h3><a href="<?=SITE_URL; ?>guideline/adopt/<?=$_GET['id']; ?>?token=<?=$_COOKIE['token']; ?>" class="btn btn-primary" style="float:right" type="button" id="adopt" name="adopt"><?=($data[0]['adopted'] == 'false' ? 'Bookmark' : 'Remove Bookmark'); ?></a>
			<span style='float:right'><input type='text' id='search' name='search' placeholder='Search...'><button id='searchbutton' value='Search' style='height:37px'>Search</button></span>
        </div>
        <?php
		if (isset($_SESSION['searchterm'])) {?>
		<span style='float:left'>Search results for: <i id="searchterm"><?=$_SESSION['searchterm']; ?></i> <a href='index.php'>clear search</a></span>
		<?php } ?>
        <div class="row">
        	<div class="col-md-12">
	        	<div class="grid simple">
	        		<div class="grid-body">
	        			<div class="col-md-8">
							<?php if (isset($data[0]['warning']) && $data[0]['warning'] !== '') { ?>
							  <div class="alert panel-group" id="accordion" data-toggle="collapse">
								  <div class="panel panel-default">
									<div class="panel-heading collapsed">
									  <h4 class="panel-title"><a class="collapse" data-toggle="collapse" data-parent="#accordion" href="#collapsewarning"><?=$data[0]['warningtitle'] ?? 'Warning'; ?></a></h4>
									</div>
									<div id="collapsewarning" class="panel-collapse collapse in">
										<div class="panel-body">
													<p><?=(isset($_SESSION['searchterm']) && $_SESSION['searchterm'] !== '' ? \preg_replace('/' . $_SESSION['searchterm'] . '/i', "<span class='highlight'>\$0</span>", $data[0]['warning']) : $data[0]['warning']); ?></p>
										</div>
									</div>
								</div>
							  </div>
						  <?php } ?>
		        			<p><?=(isset($_SESSION['searchterm']) && $_SESSION['searchterm'] !== '' ? \preg_replace('/' . $_SESSION['searchterm'] . '/i', "<span class='highlight'>\$0</span>", $data[0]['text']) : $data[0]['text']); ?></p>
		        			<br/>
		        				<?php include 'inc/content-accordion.php'; ?>
		        			<?php if (\count($data[0]['files']) > 0) {?>
		        			<h4>Download Center</h4>
								<div class="tiles white added-margin">
								  <table class="table no-more-tables">
									<thead >
									  <tr>
										<th style="width:9%">File Type</th>
										<th style="width:22%">Name</th>
										<th style="width:6%">Size</th>
										<th style="width:1%"> </th>
									  </tr>
									</thead>
									<tbody>
									<?php foreach ($data[0]['files'] as $key => $item) {?>
									  <tr>
										<td class="v-align-middle bold text-success"><a href="" class="text-success"><i class="fa fa-cloud-download"></i> PDF</a></td>
										<td class="v-align-middle"><span class="muted"><a href='download.php?file=<?=$item['filename']; ?>&filename=<?=$item['dl_filename']; ?>' target="_blank"><?=($item['dl_filename'] !== '' ? $item['dl_filename'] : $item['filename']); ?></a></span> </td>
										<td><span class="muted bold text-success"><?=$item['filesize'] !== '' ? \floor($item['filesize'] / 1000) : \floor(\filesize('../uploads/' . $item['filename']) / 1000); ?>Kb</span> </td>
										<td class="v-align-middle"></td>
									  </tr>
									  <?php }?>
									</tbody>
								  </table>
					        </div>
					        <?php }?>
					         <h4>Citation References</h4>
					        <div class="citations clearfix" >
										<div class="row">
											<?php

											if ($data[0]['guideline_parent_id'] > 0 || $data[0]['link'] > 0) {?>
													<div class="col-md-8">
								        	Source: <?=$data[0]['guideline_parent_network']; ?> and adapted for use for <?=$data[0]['network']; ?>
								        	</div>
											<?php } ?>
					        	</div>
								<?php foreach ($data[0]['citations'] as $key => $item) {?>
					        	<div class="row">

						        	<div class="col-md-3">
						        	<a href="#"><?=$item['author']; ?></a>
						        	</div>
						        	<div class="col-md-8">
						        	<p><?=$item['reference']; ?></p>
						        	</div>
					        	</div>
					        	<?php }?>
					        </div>
					        <h4>Comments</h4>
					        <div class="comments-section popover-content" style="border-top:2px solid;">
							<div class="input-group">
							<form id="commentform" name="commentform" method="post" action="<?=SITE_URL; ?>comment/addcomments">
							  <input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
							  <input type="hidden" name="id" id="id" value="<?=$_GET['id']; ?>">
							  <span class="input-group-addon primary">
							  <span class="arrow"></span>
								<i class="fa fa-comment"></i>
							  </span>
							  <input type="text" class="form-control" name="comment" id="comment" placeholder="Write a comment...">
								<input type="submit" class="btn btn-primary" style="float:right !important">
								</form>
								<p>Please note: comments are sent directly to the Guideline owner and are not published</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>


	</div>


</div>


<div>
	<div class="tiles grey added-margin" style="margin-bottom:5px;">
	  <div class="tiles-body">
		<?php
			if ($data[0]['logo'][0]['filename'] !== '') {
				if ($data[0]['cloned'] == 'true') {
					?>
		  <div><p style="color:#000">Information provided by:</p></div>
				<?php
				} ?>
			<div class="network-logo" style="width:320px; height: 100px; background:#fff;padding-top:10px" align="center">
				<img src="/frontend/download.php?file=<?=$data[0]['logo'][0]['filename']; ?>&filename=<?=$data['logo'][0]['dl_filename']; ?>">
			</div>
		<?php
			}
		?>
		<table class="table no-more-tables m-t-20">
			<thead style="display:none">
			  <tr>
				<th style="width:10%">Field</th>
				<th style="width:80%">Description</th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<td class="v-align-middle bold text-success">Date Created:</td>
				<td class="v-align-middle"><span class="muted"><?=$data[0]['date_created']; ?></span> </td>
			  </tr>
			  <tr>
				<td class="v-align-middle bold text-success">Last updated:</td>
				<td class="v-align-middle"><span class="muted"><?=$data[0]['history'][0]['mdate']; ?></span> </td>
			  </tr>
			  <tr>
				<td class="v-align-middle bold text-success">Location:</td>
				<td class="v-align-middle"><span class="muted"><?=$data[0]['country']; ?></span> </td>
			  </tr>
			  <tr>
				<td class="v-align-middle bold text-success">Category:</td>
				<td class="v-align-middle"><span class="muted"><?=$data[0]['categories']; ?></span> </td>
			  </tr>
			  <tr>
				<td class="v-align-middle bold text-success">Type:</td>
				<td class="v-align-middle"><span class="muted"><?=$data[0]['type']; ?></span> </td>

			  </tr>
			</tbody>
		  </table>


	  </div>
	</div>

</div>





	<!--END PAGE CONTENT-->


<?php include 'inc/scripts.php'; ?>
<script src="assets/js/pages/guide.js" type="text/javascript"></script>
<?php include 'inc/footer.php'; ?>
