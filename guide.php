<?php include('functions.php') ?>
<?php
	$data = makeRequest("guideline/getguide/".$_GET['id'],"GET");
?>
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
<input type="hidden" id="network_ga" value="<?=$data[0]['network_ga']?>">

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include('inc/sidebar.php') ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="clearfix"></div>
    <div class="content">
		<div class="page-title">
        	<h3><?=preg_replace("/".$_SESSION['searchterm']."/i","<span class='highlight'>\$0</span>",$data[0]['title'])?></h3>
			<span style='float:right'><input type='text' id='search' name='search' placeholder='Search...'><button id='searchbutton' value='Search' style='height:37px'>Search</button></span>
        </div>
        <?php
		if(isset($_SESSION['searchterm'])){?>
		<span style='float:left'>Search results for: <i id="searchterm"><?=$_SESSION['searchterm']?></i> <a href='index.php'>clear search</a></span>
		<?php } ?>
        <div class="row">
        	<div class="col-md-12">
	        	<div class="grid simple">
	        		<div class="grid-body">
	        			<div class="col-md-8">
							<?php if(isset($data[0]['warning']) && $data[0]['warning']!=""){ ?>
							  <div class="alert panel-group" id="accordion" data-toggle="collapse">
								  <div class="panel panel-default">
									<div class="panel-heading collapsed">
									  <h4 class="panel-title"><a class="collapse" data-toggle="collapse" data-parent="#accordion" href="#collapsewarning"><?=isset($data[0]['warningtitle'])?$data[0]['warningtitle']:"Warning"?></a></h4>
									</div>
									<div id="collapsewarning" class="panel-collapse collapse in">
										<div class="panel-body">
													<p><?=(isset($_SESSION['searchterm']) && $_SESSION['searchterm']!=""?preg_replace("/".$_SESSION['searchterm']."/i","<span class='highlight'>\$0</span>",$data[0]['warning']):$data[0]['warning'])?></p>
										</div>
									</div>
								</div>
							  </div>
						  <?php } ?>
						  <?php if(count($data[0]['decision_assists'])>0 && $data[0]['decision_assists'][0]['id']!=null){?>
							<div class="tree-view-boxes clearfix">
							<?php foreach($data[0]['decision_assists'] as $key=>$value){?>

				                <div class="tiles tree-view-box white" >
				                  <div class="tiles-body">
									<a href='tree-view.php?id=<?=$_GET['id']?>&decision_id=<?=$value['id']?>'><h4 class=""><?=$value['treeview']?></h4></a>
									<br />
								</div>
			                  </div>

							<?php }?>
						</div>
						<?php }?>
		        			<p><?=(isset($_SESSION['searchterm']) && $_SESSION['searchterm']!=""?preg_replace("/".$_SESSION['searchterm']."/i","<span class='highlight'>\$0</span>",$data[0]['text']):$data[0]['text'])?></p>
		        			<br/>
		        				<?php include('inc/content-accordion.php') ?>
		        			<?php if(count($data[0]['files'])>0){?>
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
									<?php foreach($data[0]['files'] as $key=>$item){?>
									  <tr>
										<td class="v-align-middle bold text-success"><a href="" class="text-success"><i class="fa fa-cloud-download"></i> PDF</a></td>
										<td class="v-align-middle"><span class="muted"><a href='download.php?file=<?=$item['filename']?>&filename=<?=$item['dl_filename']?>' target="_blank"><?=($item['dl_filename']!=""?$item['dl_filename']:$item['filename'])?></a></span> </td>
										<td><span class="muted bold text-success"><?=$item['filesize']!=""?floor($item['filesize']/1000):floor(filesize("../uploads/".$item['filename'])/1000)?>Kb</span> </td>
										<td class="v-align-middle"></td>
									  </tr>
									  <?php }?>
									</tbody>
								  </table>
					        </div>
					        <?php }?>
					         <h4>Citation References</h4>
					        <div class="citations clearfix" >
								<?php foreach($data[0]['citations'] as $key=>$item){?>
					        	<div class="row">
						        	<div class="col-md-3">
						        	<a href="#"><?=$item['author']?></a>
						        	</div>
						        	<div class="col-md-8">
						        	<p><?=$item['citation']?></p>
						        	</div>
					        	</div>
					        	<?php }?>
					        </div>
									<h4>User Comments</h4>
									<div class="comments-section popover-content" style="border-top:2px solid;">
										<?php foreach($data[0]['comments'] as $key=>$item){?>
									<div class="notification-messages white">
											<div class="user-profile">
												<img src="../assets/img/profiles/bc.jpg" alt="" data-src="assets/img/profiles/bc.jpg" data-src-retina="assets/img/profiles/bc2x.jpg" width="35" height="35">
											</div>
											<div class="message-wrapper">
												<div class="heading">
													<?=$item['member']?>
												</div>
												<div class="description">
													<?=$item['comment']?>
												</div>
												<div class="date pull-left">
													<?=$item['cdate']?>
												</div>
											</div>
											<div class="clearfix"></div>
										</div>
										<?php }?>
									</div>
									<h4>Change History</h4>
									<div class="comments-section popover-content" style="border-top:2px solid;">
										<?php foreach($data[0]['history'] as $key=>$item){?>
										<div class="notification-messages white">
											<div class="message-wrapper" style="width:330px">
												<div>
													<strong>Date and Time:</strong> <?=$item['mdate']?><br />
													<strong>Change by:</strong> <?=$item['email']?></br />
													<strong>Note:</strong> <?=$item['comment']?>
												</div>
											</div>
											<div class="clearfix"></div>
										</div>
										<?php }?>
									</div>
									<div>
										<div class="tiles grey added-margin" style="margin-bottom:5px;">
										  <div class="tiles-body">
											<?php
												if($data[0]['logo'][0]['filename']!=""){
													if($data[0]['cloned']=="true"){
											?>
											  <div><p style="color:#000">Information provided by:</p></div>
													<?php } ?>
												<div class="network-logo" style="width:320px; height: 100px; background:#fff;padding-top:10px" align="center">
													<img src="/frontend/download.php?file=<?=$data[0]['logo'][0]['filename']?>&filename=<?=$data['logo'][0]['dl_filename']?>">
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
													<td class="v-align-middle"><span class="muted"><?=$data[0]['date_created']?></span> </td>
												  </tr>
												  <tr>
													<td class="v-align-middle bold text-success">Last updated:</td>
													<td class="v-align-middle"><span class="muted"><?=$data[0]['date_modified']?></span> </td>
												  </tr>
												  <tr>
													<td class="v-align-middle bold text-success">Location:</td>
													<td class="v-align-middle"><span class="muted"><?=$data[0]['country']?></span> </td>
												  </tr>
												  <tr>
													<td class="v-align-middle bold text-success">Category:</td>
													<td class="v-align-middle"><span class="muted"><?=$data[0]['categories']?></span> </td>
												  </tr>
												  <tr>
													<td class="v-align-middle bold text-success">Type:</td>
													<td class="v-align-middle"><span class="muted"><?=$data[0]['type']?></span> </td>

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


	</div>


</div>








	<!--END PAGE CONTENT-->


<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/guide.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
