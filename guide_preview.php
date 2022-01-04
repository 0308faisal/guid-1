<?php declare(strict_types=1);
include 'functions.php'; ?>
<?php
	$data = makeRequest('guideline/getguide/' . $_GET['id'], 'GET');
?>
<?php include 'inc/preview_header.php'; ?>
<body class="home" style="padding-top:0px !important">


	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="page-title">
        	<h3><?=$data[0]['title']; ?></h3>
        </div>

        <div class="row">
        	<div class="col-md-12">
	        	<div class="grid simple">
	        		<div class="grid-body">
	        			<div class="col-md-8">
							<?php if (isset($data[0]['warning']) && $data[0]['warning'] !== '') { ?>
							  <div class="alert panel-group" id="accordion" data-toggle="collapse">
								  <div class="panel panel-default">
									<div class="panel-heading collapsed">
									  <h4 class="panel-title"><a class="collapse" data-toggle="collapse" data-parent="#accordion" href="#collapsewarning">Warning</a></h4>
									</div>
									<div id="collapsewarning" class="panel-collapse collapse in">
										<div class="panel-body">
													<p><?=$data[0]['warning']; ?></p>
										</div>
									</div>
								</div>
							  </div>
						  <?php } ?>
						  <?php if (\count($data[0]['decision_assists']) > 0 && $data[0]['decision_assists'][0]['id'] !== null) {?>
							<div class="tree-view-boxes clearfix">
							<?php foreach ($data[0]['decision_assists'] as $key => $value) {?>

				                <div class="tiles tree-view-box white" >
				                  <div class="tiles-body">
									<a href='guide_treeview.php?id=<?=$_GET['id']; ?>&decision_id=<?=$key; ?>'><h4 class=""><?=$value['treeview']; ?></h4></a>
									<br />
								</div>
			                  </div>

							<?php }?>
						</div>
						<?php }?>
		        			<p><?=$data[0]['text']; ?></p>
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
										<td class="v-align-middle"><span class="muted"><a href='/uploads/<?=$item['filename']; ?>'><?=$item['filename']; ?></a></span> </td>
										<td><span class="muted bold text-success"><?=\floor(\filesize('../uploads/' . $item['filename']) / 1000); ?>Kb</span> </td>
										<td class="v-align-middle"></td>
									  </tr>
									  <?php }?>
									</tbody>
								  </table>
					        </div>
					        <?php }?>
					         <h4>Citation References</h4>
					        <div class="citations clearfix" >
								<?php foreach ($data[0]['citations'] as $key => $item) {?>
					        	<div class="row">
						        	<div class="col-md-3">
						        	<a href="#"><?=$item['author']; ?></a>
						        	</div>
						        	<div class="col-md-8">
						        	<p><?=$item['citation']; ?></p>
						        	</div>
					        	</div>
					        	<?php }?>
					        </div>
					        <h4>Comments(<?=\count($data[0]['comments']); ?>)</h4>
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
							<div class="input-group" style="display:none;">
							<form id="commentform" name="commentform" method="post" action="<?=SITE_URL; ?>comment/addcomments">
							  <input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
							  <input type="hidden" name="id" id="id" value="<?=$_GET['id']; ?>">
							  <span class="input-group-addon primary">
							  <span class="arrow"></span>
								<i class="fa fa-comment"></i>
							  </span>
							  <input type="text" class="form-control" name="comment" id="comment" placeholder="Write a comment...">
							  </form>
				</div>
			</div>
		  </div style="display:none">
	        			<div class="col-md-4 sidebar-meta" >
	        				<div class="tiles grey added-margin" style="margin-bottom:5px;">
			                  <div class="tiles-body">
			                    <div class="network-logo" style="width:320px; height: 100px; background:#fff;">
			                    	<img src="frontend/assets/images/network-logo.jpg">
			                    </div>
			                    <table class="table no-more-tables m-t-20">
									<thead style="display:none">
									  <tr>
										<th style="width:10%">Field</th>
										<th style="width:80%">Description</th>
									  </tr>
									</thead>
									<tbody>
									  <tr>
										<td class="v-align-middle bold text-success">Last updated:</td>
										<td class="v-align-middle"><span class="muted"><?=$data[0]['date_modified']; ?></span> </td>
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

	        		</div>
	        	</div>
        	</div>


        </div>


	</div>







	<!--END PAGE CONTENT-->


<?php include 'frontend/inc/scripts.php'; ?>
<script src="frontend/assets/js/pages/guide.js" type="text/javascript"></script>
</body>
</html>
