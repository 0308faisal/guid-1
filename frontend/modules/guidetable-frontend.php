<?php

	$search=isset($_GET['searchterm'])?"?searchterm=".$_GET['searchterm']:"";
	$data = makeRequest("guideline/getguides".$search,"GET");
	$published_guides=$unpublished_guides=array();
  foreach($data["published_guides"] as $key=>$array){
		$categories=explode(",",$array['category']);
		sort($categories);
		foreach($categories as $category){
			$published_guides[trim(ucwords(strtolower($category)))][]=$array;
		}
  }
  foreach($data["unpublished_guides"] as $key=>$array){
		$categories=explode(",",$array['category']);
		sort($categories);
		foreach($categories as $category){
			$unpublished_guides[trim(ucwords(strtolower($category)))][]=$array;
		}
  }
  ksort($published_guides);
  ksort($unpublished_guides);
  function mysort ($x, $y) {
	  return strcasecmp($x['title'], $y['title']);
  }

?>
<?php
if(isset($_SESSION['searchterm'])){?>
<span style='float:left'>Search results for: <i id="searchterm"><?=$_SESSION['searchterm']?></i> <a href='index.php'>clear search</a></span>
<?php } ?>



    <div class="row-fluid">
        <div class="span12">
          <div class="grid simple">
            <div class="grid-title">
              <h4>Network Guides</h4>

            </div>
            <div class="grid-body ">
              <div class="panel-group" id="accordion" data-toggle="collapse">
					<?php
						$counter=0;
						foreach($published_guides as $key=>$array){
								$counter++;
								usort($array, 'mysort');

					?>
							 <div class="panel panel-default">
								<div class="panel-heading collapsed">
								  <h4 class="panel-title">
									  <a class="<?=(isset($_SESSION['searchterm'])?'':'collapsed')?> catcollapse" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$counter?>">
										   <?=$key?>&nbsp;
										</a></h4>
								</div>
								<div id="collapse<?=$counter?>" class="panel-collapse  <?=(isset($_SESSION['searchterm'])?'':'collapse" style="height: 0px;')?>">
								  <div class="panel-body">

									  <table class="table guidetable" id="guidetable" >
										<thead>
										  <tr>
											<th>Guide Name</th>
											<th>Categories</th>
										  </tr>
										</thead>
										<tbody>
										  <?php foreach($array as $subkey=>$subarray){?>
											<tr class="odd gradeX">
												<td><a name="<?=$key."|".$subarray['title'];?>" id="<?="[".$subarray['title']."]";?>" class="guidelink" href="guide.php?id=<?=$subarray['id']?><?=isset($_SESSION['searchterm'])?"&searchterm=".$_SESSION['searchterm']:""?>"><?=preg_replace("/".(isset($_SESSION['searchterm'])?$_SESSION['searchterm']:"")."/i","<span class='highlight'>\$0</span>",$subarray['title'])?></a></td>
												<td><?=$subarray['category']?></td>
											  </tr>
										<?php } ?>
										</tbody>
									  </table>
									   </div>
								</div>
							  </div>
						<?php
						}
						?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4>My Guidelines</h4>

            </div>
            <div class="grid-body ">
				<div class="panel-group" id="accordion" data-toggle="collapse">

							 <div class="panel panel-default">
								  <div class="panel-body">
									  <table class="table guidetable" id="guidetable" >
										<thead>
										  <tr>
											<th>Guide Name</th>
											<th>Categories</th>
											<!--<th>Network</th>
											<th>Last Updated</th>
											<th>Location</th>-->
										  </tr>
										</thead>
										<tbody>
											<?php
												foreach($unpublished_guides as $key=>$array){
													foreach($array as $subkey=>$subarray){
											?>
											<tr class="odd gradeX">
												<td><a class="guidelink" href="guide.php?id=<?=$subarray['id']?>"><?=preg_replace("/".$_SESSION['searchterm']."/i","<span class='highlight'>\$0</span>",$subarray['title'])?></a></td>
												<td><?=$subarray['category']?></th>
												<!--<td><?=$subarray['network']?></td>
												<td><?=$subarray['lastupdate']?></th>
												<td><?=$subarray['country']?></td>-->
											  </tr>
										<?php }
											} ?>
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
