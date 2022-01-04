<?php

declare(strict_types=1);

if (isset($_GET['searchterm'])) {
  $search = isset($_GET['searchterm']) ? '&searchterm=' . $_GET['searchterm'] : '';
  $_SESSION['searchterm'] = $_GET['searchterm'];
} else {
  unset($_SESSION['searchterm']);
}
$data = makeRequest('guideline/guidemanagementsummary' . $search, 'GET');
$published_guides = $unpublished_guides = [];

foreach ($data['published_guides'] as $key => $array) { 
  $published_guides[$array['network_id']][] = $array;
}

foreach ($data['unpublished_guides'] as $key => $array) {
  $unpublished_guides[$array['network_id']][] = $array;
}

foreach ($user->networks as $key => $network) {
  $usernetworks[] = $network['id'];
}
$networkdata = makeRequest('network/networkaccesslist?restricted=1&n2n=' . $_SESSION['activenetwork'], 'GET');

foreach ($networkdata['networks'] as $key => $item) {
  $networks[$item['network_id']] = $item['name'];

  if ($item['member'] == 0) {
    $item['status'] = false;
  }

  switch ($item['status']) {
    case 'active':
      if (!in_array($item['network_id'], $usernetworks)) {
        $usernetworks[] = $item['network_id'];
      }
      $buttonscript[$item['network_id']] = 'leavenetwork';
      $buttontext[$item['network_id']] = 'Leave';
      break;
    case 'pending':
      $buttonscript[$item['network_id']] = 'leavenetwork';
      $buttontext[$item['network_id']] = 'Access Requested';
      break;
    default:
      $buttonscript[$item['network_id']] = 'joinnetwork';
      $buttontext[$item['network_id']] = 'Request Access';
  }
}
?>

<?php
if (isset($_SESSION['searchterm'])) {
?>
  <span style='float:left'>Search results for: <i id="searchterm"><?= $_SESSION['searchterm']; ?></i> <a href='index.php'>clear search</a></span>
<?php
} ?>

<div class="row-fluid">
  <div class="span12">
    <div class="grid simple ">
      <div class="grid-title">
        <h4>My Network Guidelines</h4>
      </div>
      <div class="grid-body ">
        <div class="panel-group" id="accordion" data-toggle="collapse">
          <div class="panel panel-default">
            <?php
            foreach ($published_guides as $network_id => $array) {
              if ($network_id == $_SESSION['activenetwork']) {
            ?>
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#mycollapse<?= $network_id; ?>">
                        <?= $networks[$network_id]; ?>&nbsp;<input type="button" class="btn btn-success" style="float:right;margin-top:-7px" value="My Network" />
                      </a>
                    </h4>
                  </div>
                  <div id="mycollapse<?= $network_id; ?>" class="panel-collapse collapse" style="height: 0px;">
                    <div class="panel-body">
                      <table class="table guidetable">
                        <thead>
                          <tr>
                            <th style="width:525px">Guide Name</th>
                            <th>Categories</th>
                            <th>Source</th>
                            <!--<th>Network</th>
                          <th>Last Updated</th>
                          <th>Location</th>-->
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>

                          <?php foreach ($array as $subkey => $subarray) {
                            $version = $subarray['parent_id'] > 0 ? 'copy' : 'original';
                            $version = $subarray['link'] == '1' ? 'link' : $version;
                            $deletelink = '';

                            if ($subarray['author'] == $user->id) {
                              $deletelink = "<a class='delete' style=\"background: url('assets/img/icon/portlet-tray.png') no-repeat;background-position: -67px -32px;width: 15px;height: 22px;display:inline-block;\" href='" . SITE_URL . 'guideline/guidedelete?id=' . $subarray['id'] . '&token=' . $_COOKIE['token'] . "'>&nbsp;</a>";
                            } elseif ($subarray['link'] == 1) {
                              $deletelink = "<a class='unlink'  style=\"background: url('assets/img/icon/portlet-tray.png') no-repeat;background-position: -67px -32px;width: 15px;height: 22px;display:inline-block;\" href='" . SITE_URL . 'guideline/unlinkguide?id=' . $subarray['id'] . '&activenetwork=' . $_SESSION['activenetwork'] . '&token=' . $_COOKIE['token'] . "'>&nbsp;</a>";
                            }

                            if (in_array($network_id, $usernetworks)) {
                          ?>
                              <tr class="odd gradeX">
                                <td><?= $deletelink; ?><a class="guidelink" href="<?= $subarray['link'] !== '1' ? 'guideline_edit.php' : 'guide.php'; ?>?id=<?= $subarray['id']; ?>"><?= $subarray['title']; ?></a></td>
                                <td><?= $subarray['category']; ?></th>
                                <td><?= $version; ?></th>
                                <td><?php if ($network_id !== $_SESSION['activenetwork']) {
                                    ?><input type="button" id="linkbutton_<?= $subarray['id']; ?>" data-guideline_id="<?= $subarray['id']; ?>" class="btn btn-primary link" value="Link" /> &nbsp;<input type="button" id="copybutton_<?= $subarray['id']; ?>" data-guideline_id="<?= $subarray['id']; ?>" class="btn btn-primary copy" value="Copy" /><?php
                                                                                                                                                                                                                                                                                                                                                      } ?></td>
                              </tr>
                            <?php
                            } else {
                            ?>
                              <tr class="odd gradeX">
                                <td><?= $deletelink; ?><a class="guidelink" href="<?= $subarray['link'] !== '1' ? 'guideline_edit.php' : 'guide.php'; ?>?id=<?= $subarray['id']; ?>"><?= $subarray['title']; ?></a></td>
                                <td><?= $subarray['category']; ?></th>
                                <td><?= $version; ?></th>
                                <td></td>
                              </tr>
                            <?php
                            } ?>
                          <?php
                          } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            <?php
              }
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
            foreach ($unpublished_guides as $network_id => $array) {
              if (isset($networks[$network_id])) {
            ?>
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed catcollapse" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $network_id; ?>">
                        <?= $networks[$network_id]; ?>&nbsp;
                      </a>
                    </h4>
                  </div>
                  <div id="collapse<?= $network_id; ?>" class="panel-collapse collapse" style="height: 0px;">
                    <div class="panel-body">
                      <table class="table guidetable">
                        <thead>
                          <tr>
                            <th>Guide Name</th>
                            <th>Categories</th>
                            <th>Network</th>
                            <th>Last Updated</th>
                            <th>Location</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($array as $subkey => $subarray) {
                          ?>
                            <tr class="odd gradeX">
                              <td><?php if ($subarray['author'] == $user->id) {
                                  ?><a class="delete" style="background: url('assets/img/icon/portlet-tray.png') no-repeat;background-position: -67px -32px;width: 15px;height: 22px;display:inline-block;" href="<?= SITE_URL; ?>guideline/guidedelete?id=<?= $subarray['id']; ?>&nid=<?= $network_id; ?>">&nbsp;</a><?php
                                                                                                                                                                                                                                                                                                                    } ?><a class="guidelink" href="<?= $subarray['link'] !== '1' ? 'guideline_edit.php' : 'guide.php'; ?>?id=<?= $subarray['id']; ?>"><?= $subarray['title']; ?></a></td>
                              <td><?= $subarray['category']; ?></th>
                              <td><?= $subarray['network']; ?></td>
                              <td><?= $subarray['lastupdate']; ?></th>
                              <td><?= $subarray['country']; ?></td>
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
          <h4>Other Network Guidelines</h4>
        </div>
        <div class="grid-body ">
          <div class="panel-group" id="accordion" data-toggle="collapse">
            <div class="panel panel-default">
              <?php
              foreach ($published_guides as $network_id => $array) {
                if ($network_id !== $_SESSION['activenetwork']) {
                  if (isset($networks[$network_id])) {
              ?>
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#mycollapse<?= $network_id; ?>">
                            <?php if (in_array($network_id, $usernetworks)) {
                            ?>
                              <?php if ($network_id == $_SESSION['activenetwork']) {
                              ?>
                                <?= $networks[$network_id]; ?>&nbsp;<input type="button" class="btn btn-success" style="float:right;margin-top:-7px" value="My Network" />
                              <?php
                              } else {
                              ?>
                                <?= $networks[$network_id]; ?>&nbsp;<input type="button" id="copyall_<?= $network_id; ?>" data-networkid="<?= $network_id; ?>" class="btn btn-primary copyall" style="float:right;margin-top:-7px" value="Copy All" />
                              <?php
                              } ?>
                            <?php
                            } else {
                            ?>
                              <?= $networks[$network_id]; ?>&nbsp;<input type="button" id="netbutton_<?= $network_id; ?>" data-networkname="<?= $networks[$network_id]; ?>" data-networkid="<?= $network_id; ?>" class="btn btn-primary <?= $buttonscript[$network_id]; ?>" style="float:right;margin-top:-7px" value="<?= $buttontext[$network_id]; ?>">
                            <?php
                            } ?>
                          </a>
                        </h4>
                      </div>
                      <div id="mycollapse<?= $network_id; ?>" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <table class="table guidetable">
                            <thead>
                              <tr>
                                <th style="width:525px">Guide Name</th>
                                <th style="width:400px">Categories</th>
                                <!--<th>Network</th>
                            <th>Last Updated</th>
                            <th>Location....</th>-->
                                <th></th>
                              </tr>
                            </thead>
                            <tbody>

                              <?php

                              foreach ($array as $subkey => $subarray) {
                                /*if ($user->id == 129) {
                                  echo "session:" . $_SESSION['activenetwork'] . PHP_EOL;
                                  echo "network:" . $network_id . PHP_EOL;
                                  print_r($usernetworks);
                                  echo "in array:" . in_array($network_id, $usernetworks) . PHP_EOL;
                                }*/
                                if (in_array($network_id, $usernetworks)) {
                              ?>
                                  <tr class="odd gradeX">
                                    <td>
                                      <?php if ($subarray['author'] == $user->id) { ?>
                                        <a class="delete" href="<?= SITE_URL; ?>guideline/guidedelete?id=<?= $subarray['id']; ?>&token=<?= $_COOKIE['token']; ?>">&nbsp;</a>
                                      <?php } ?>
                                      <a class="guidelink" href="guide.php?id=<?= $subarray['id']; ?>"><?= $subarray['title']; ?></a>
                                    </td>
                                    <td><?= $subarray['category']; ?></th>
                                    <td>
                                      <?php if ($network_id !== $_SESSION['activenetwork']) { ?>
                                        <input type="button" id="linkbutton_<?= $subarray['id']; ?>" data-guideline_id="<?= $subarray['id']; ?>" class="btn btn-primary link" value="Link" /> &nbsp;<input type="button" id="copybutton_<?= $subarray['id']; ?>" data-guideline_id="<?= $subarray['id']; ?>" class="btn btn-primary copy" value="Copy" />
                                      <?php } ?>
                                    </td>
                                  </tr>
                                <?php
                                } else {
                                ?>
                                  <tr class="odd gradeX">
                                    <td><?= $subarray['title']; ?></td>
                                    <td><?= $subarray['category']; ?></th>
                                    <td></td>
                                  </tr>
                                <?php
                                } ?>
                              <?php
                              } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
              <?php
                  }
                }
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>