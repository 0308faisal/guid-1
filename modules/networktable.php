<?php
	$data = makeRequest("network/networkaccesslist?restricted=1&n2n=".$_SESSION['activenetwork'],"GET");
	foreach($user->networks as $key=>$networks){
    $usernetworks[]=$networks['id'];
  }

?>
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">
			<div class="grid-title">
				<h4>My Networks</h4>
				<div class="tools"> <a href="javascript:;" class="collapse"></a></a> <a href="javascript:;" class="reload"></a></a> </div>
			</div>
			<div class="grid-body ">
				<table class="table" id="example3" >
				<thead>
				<tr>
				<th>Network Name</th>
				<th>Guidelines</th>
				<th>Location</th>
				<th>Description</th>
				<!--<th>Created</th>-->
				<th></th>
				</tr>
				</thead>
				<tbody>
					<?php foreach($data["networks"] as $key=>$item){
						if($item['member']==0){
						  $item['status']=false;
					  }
						switch($item['network_type']){
							case "public":
								$networktype=0;
								break;
							default:
								$networktype=1;
								break;
						}
					  switch($item['status']){
						  case "active":
							$buttonscript="leavenetwork({$item['network_id']},'{$_SESSION['activenetwork']}','{$networktype}')";
							$buttontext="Leave";
						  break;
						  case "pending":
							$buttonscript="leavenetwork({$item['network_id']},'{$_SESSION['activenetwork']}','{$networktype}')";
							$buttontext="Access Requested";
						  break;
						  default:
							if($networktype==1){
								$buttonscript="requestnetwork({$item['network_id']},'{$_SESSION['activenetwork']}','{$networktype}')";
								$buttontext="Request Access";
							}
							else{
								$buttonscript="joinnetwork({$item['network_id']},'{$_SESSION['activenetwork']}','{$networktype}')";
								$buttontext="Access Available";
							}
					  }
					  ?>
						<tr class="odd gradeX">
						<td>
							<?php if(in_array($item['network_id'],$usernetworks) && $item['network_id']==$_SESSION['activenetwork']){?>
								<a href="network_edit.php?id=<?=$item['network_id']?>"><?=$item['name']?></a><?php }
								else{?>
								<?=$item['name']?>
								<?php }?></td>
						<td><?=$item['guidelines']?></td>
						<td><?=$item['country']?></td>
						<td><?=$item['description']?></td>
						<!--<td><?=$item['cdate']?></td>-->
						<?php if($item['network_id']==$_SESSION['activenetwork']){?>
							<td><input type="button" class="btn btn-success" style="float:right;margin-top:-7px" value="My Network" /></td>
						<?php }
						else{ ?>
							<td><input type="button" id="netbutton_<?=$item['network_id']?>" onclick="<?=$buttonscript?>" class="btn btn-primary"  style="float:right" value="<?=$buttontext?>"></td>
					  <?php } ?>
						</tr>
					<?php } ?>
				</tbody>
				</table>
            </div>
          </div>
        </div>
      </div>
    </div>
