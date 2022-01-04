<?php
    $data = makeRequest('user/userlist/'.$_GET['id'], 'GET');
?>
<div class="row-fluid">
  <div class="span12">
    <div class="grid simple ">
      <div class="grid-title no-border">
        <h4></h4>
      </div>
      <div class="grid-body no-border">
        <table class="table" id="membertable" >
          <thead>
            <tr>
              <th>ID</th>
              <th>Email</th>
              <th>Name</th>
              <th>Grade</th>
              <th>Profession</th>
              <th>Speciality</th>
              <th>Networks</th>
							<th>Status</th>
              <!--<th>Department</th>-->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['members'] as $key => $array) {
    if ($array['profile'] == 'Admin') {
        $array['status'] = $array['profile']; 
    }
    ?>
            <tr class="odd gradeX">
              <td class="idfield"><?=$array['id']?></th>
              <td><?=$array['email']?></th>
              <td><?=$array['fname']?> <?=$array['lname']?></td>
              <td><?=$array['grade']?></th>
              <td><?=$array['occupation']?></td>
              <td><?=$array['speciality']?></td>
              <td><?php
                $networks=explode(",",$array['network']);
                foreach($networks as $subkey=>$network){
                  $text=explode("-",$network);
                  $networktext[]=$text[0]." - ".$text[1];
                }
                echo implode(",",$networktext);
                unset($networktext);
              ?></td>
							<td style="width:200px">
								<select class="form-control statusselect" style="height:20px !important;">
                  <?php
                    $networks=explode(",",$array['network']);
                    foreach($networks as $subkey=>$network){
                      $status=explode("-",$network);
                      if($status[1]=="PENDING"){
                        echo "<option value='active|".$status[2]."'>Approve - ".$status[0]."</option>";
                      }
                      echo "<option value='banned|".$status[2]."' ".($array['status']=='banned'?'selected':'').">Ban - ".$status[0]."</option>
    									<option value='remove|".$status[2]."' ".($array['status']=='remove'?'selected':'').">Delete - ".$status[0]."</option>
                      ";
                    }
                  ?>

								</select>
								<button class="btn btn-primary btn-sm btn-small statusupdate" style="height:20px !important;padding-top:0px;margin-left:10px;" name="statusupdate">Apply</button></td>
              <!--<td>A&amp;E</th>-->
            </tr>
            <?php
} ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
