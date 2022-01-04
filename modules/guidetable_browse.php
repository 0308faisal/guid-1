<?php declare(strict_types=1);
$search = isset($_GET['searchterm']) ? '?searchterm=' . $_GET['searchterm'] : '';
  $data = makeRequest('guideline/getguides' . $search, 'GET');
  $published_guides = $unpublished_guides = [];

  foreach ($data['adopted_guides'] as $key => $array) {
  	$categories = \explode(',', $array['category']);
  	\sort($categories);

  	foreach ($categories as $category) {
  		$published_guides[\trim(\ucwords(\strtolower($category)))][] = $array;
  	}
  }

  foreach ($data['orphaned_guides'] as $key => $array) {
  	$categories = \explode(',', $array['category']);
  	\sort($categories);

  	foreach ($categories as $category) {
  		$published_guides[\trim(\ucwords(\strtolower($category)))][] = $array;
  	}
  }

  foreach ($data['unpublished_guides'] as $key => $array) {
  	$categories = \explode(',', $array['category']);
  	\sort($categories);

  	foreach ($categories as $category) {
  		$unpublished_guides[\trim(\ucwords(\strtolower($category)))][] = $array;
  	}
  }

  \ksort($published_guides);
  \ksort($unpublished_guides);

?>
<div class="row-fluid">
  <div class="span12">
    <div class="grid simple ">
      <div class="grid-title">
        <h4>My Published Guidelines</h4>
      </div>
      <div class="grid-body ">
        <div class="panel-group" id="accordion" data-toggle="collapse">
          <?php
		  $counter = 0;

		  foreach ($published_guides as $key => $array) {
		  	$counter++; ?>
          <div class="panel panel-default">
            <div class="panel-heading collapsed">
              <h4 class="panel-title">
                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#mycollapse<?=$counter; ?>">
                  <?=$key; ?>&nbsp;
                </a>
              </h4>
            </div>
            <div id="mycollapse<?=$counter; ?>" class="panel-collapse collapse" style="height: 0px;">
              <div class="panel-body">
                <table class="table guidetable" >
                  <thead>
                    <tr>
                      <th>Guide Name</th>
                      <th>Categories</th>
                      <!--<th>Network</th>
                      <th>Last Updated</th>
                      <th>Location</th>-->
                      <th>Report</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($array as $subkey => $subarray) {?>
                    <tr class="odd gradeX">
                      <td><a class="guidelink" href="guide.php?id=<?=$subarray['id']; ?>"><?=$subarray['title']; ?></a></td>
                      <td><?=$subarray['category']; ?></th>
                      <!--<td><?=$subarray['network']; ?></td>
                      <td><?=$subarray['lastupdate']; ?></th>
                      <td><?=$subarray['country']; ?></td>-->
                      <td><a href="guide_report.php"><i class="icon-custom-chart"></a></i></td>
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
    <div class="grid simple">
      <div class="grid-title">
        <h4>My Unpublished Guides</h4>
      </div>
      <div class="grid-body ">
        <div class="panel-group" id="accordion" data-toggle="collapse">
          <?php
		  $counter = 0;

		  foreach ($unpublished_guides as $key => $array) {
		  	$counter++; ?>
          <div class="panel panel-default">
            <div class="panel-heading collapsed">
              <h4 class="panel-title">
                <a class="collapsed catcollapse" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$counter; ?>">
                  <?=$key; ?>&nbsp;
                </a>
              </h4>
            </div>
            <div id="collapse<?=$counter; ?>" class="panel-collapse collapse" style="height: 0px;">
              <div class="panel-body">
                <table class="table guidetable" >
                  <thead>
                    <tr>
                      <th>Guide Name</th>
                      <th>Categories</th>
                      <th>Network</th>
                      <th>Last Updated</th>
                      <th>Location</th>
                      <th>Report</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($array as $subkey => $subarray) {?>
                    <tr class="odd gradeX">
                      <td><?php if ($subarray['author'] == $user->id) {?><a class="delete" href="<?=SITE_URL; ?>guideline/guidedelete?id=<?=$subarray['id']; ?>&token=<?=$_COOKIE['token']; ?>">&nbsp;</a><?php }?><a class="guidelink" href="<?=$subarray['link'] !== '1' ? 'guideline_edit.php' : 'guide.php'; ?>?id=<?=$subarray['id']; ?>"><?=$subarray['title']; ?></a></td>
                      <td><?=$subarray['category']; ?></th>
                      <td><?=$subarray['network']; ?></td>
                      <td><?=$subarray['lastupdate']; ?></th>
                      <td><?=$subarray['country']; ?></td>
                      <td><a href="guide_report.php"><i class="icon-custom-chart"></a></i></td>
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
