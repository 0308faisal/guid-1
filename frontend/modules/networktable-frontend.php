<?php
	$data = makeRequest("network/getnetworks","GET");
?>
<div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4>My Networks</h4>
            </div>
            <div class="grid-body ">
              <table class="table" id="example3" >
                <thead>
                  <tr>
					<th></th>
                    <th>Network Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($data as $key=>$item){
										if($item['network_type']!="hidden"){
					  					$networkname=addslashes($item['name']);
					  					$private=$item['network_type']=='private'?1:0;
										  switch($item['status']){
											  case "active":
												$buttonscript="leavenetwork({$item['id']},'{$networkname}','{$private}')";
												$buttontext="Leave";
											  break;
											  case "pending":
												$buttonscript="leavenetwork({$item['id']},'{$networkname}','{$private}')";
												$buttontext="Pending";
											  break;
											  default:
												if($private==1){
													$buttonscript="requestnetwork({$item['id']},'{$networkname}','{$private}')";
													$buttontext="Request Access";
												}
												else{
													$buttonscript="joinnetwork({$item['id']},'{$networkname}','{$private}')";
													$buttontext="Join";
												}
										  }
					  ?>
						<tr class="odd gradeX">
						<td>
							<?php if(isset($item['logo']) && $item['logo']!=''){?>
								<img style="max-width:300px" src="download.php?file=<?=$item['logo'][0]['filename']?>&filename=<?=$item['logo'][0]['dl_filename']?>"></td>
							<?php }?>
						<td><?=$item['name']?></td>
						<td><?=$item['description']?></td>
						<td><?=$item['country']?></td>
						<?php
						if($_SESSION["iplogin"] == false){
						?>
						<td><input type="button" id="netbutton_<?=$item['id']?>" onclick="<?=$buttonscript?>" class="btn btn-primary" value="<?=$buttontext?>"></td>
						<?php }?>
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
