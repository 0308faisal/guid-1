<?php declare(strict_types=1);
$search = isset($_GET['searchterm']) ? '?searchterm=' . $_GET['searchterm'] : '';
  $data = makeRequest('guideline/getguides' . $search, 'GET');
  \ksort($data);
  $published_guides = $unpublished_guides = [];

  foreach ($data['published_guides'] as $key => $array) {
  	$published_guides[$array['network']][] = $array;
  }

  foreach ($data['unpublished_guides'] as $key => $array) {
  	$unpublished_guides[$array['network']][] = $array;
  }

  foreach ($user->networks as $key => $network) {
  	$usernetworks[] = $network['network'];
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
                      <th>Report</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($array as $subkey => $subarray) {
		  		?>
                    <tr class="odd gradeX">
                      <td><?php if ($subarray['author'] == $user->id) {
		  			?><a class="delete" style="background: url('assets/img/icon/portlet-tray.png') no-repeat;background-position: -67px -32px;width: 15px;height: 22px;display:inline-block;" href="<?=SITE_URL; ?>guideline/guidedelete?id=<?=$subarray['id']; ?>&token=<?=$_COOKIE['token']; ?>">&nbsp;</a><?php
		  		} ?><a class="guidelink" href="guideline_edit.php?id=<?=$subarray['id']; ?>"><?=$subarray['title']; ?></a></td>
                      <td><?=$subarray['category']; ?></th>
                      <td><a href="guide_report.php"><i class="icon-custom-chart"></a></i></td>
                    </tr>
                    <?php
		  	} ?>
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
		  	\krsort($array);
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
                    <?php foreach ($array as $subkey => $subarray) {
		  		?>
                    <tr class="odd gradeX">
                      <td><?php if ($subarray['author'] == $user->id) {
		  			?><a class="delete" style="background: url('assets/img/icon/portlet-tray.png') no-repeat;background-position: -67px -32px;width: 15px;height: 22px;display:inline-block;" href="<?=SITE_URL; ?>guideline/guidedelete?id=<?=$subarray['id']; ?>&token=<?=$_COOKIE['token']; ?>">&nbsp;</a><?php
		  		} ?><a class="guidelink" href="guideline_edit.php?id=<?=$subarray['id']; ?>"><?=$subarray['title']; ?></a></td>
                      <td><?=$subarray['category']; ?></th>
                      <td><?=$subarray['network']; ?></td>
                      <td><?=$subarray['lastupdate']; ?></th>
                      <td><?=$subarray['country']; ?></td>
                      <td><a href="guide_report.php"><i class="icon-custom-chart"></a></i></td>
                    </tr>
                    <?php
		  	} ?>
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
