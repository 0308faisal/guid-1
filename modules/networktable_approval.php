<?php
$data = makeRequest("network/networkapprovallist", "GET");
foreach($user->networks as $key => $networks)
{
	$usernetworks[] = $networks['network_id'];
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
				<table class="table" id="networktable" >
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
						<?php
						foreach($data["networks"] as $key => $item)
						{
							?>
							<tr class="odd gradeX">
								<td>
									<a href="network_edit.php?id=<?= $item['network_id'] ?>"><?= $item['name'] ?></a>
								</td>
								<td><?= $item['guidelines'] ?></td>
								<td><?= $item['country'] ?></td>
								<td><?= $item['description'] ?></td>
								<!--<td><?= $item['cdate'] ?></td>-->
								<td style="width:250px">
									<select id="statusselect<?=$item['network_id']?>" class="form-control" style="height:20px !important;width:150px;float:left">
										<option value="approved" <?=$item['status']=="approved"?"selected":""?>>Approved</option>
										<option value="pending" <?=$item['status']=="pending"?"selected":""?>>Pending</option>
										<option value="disabled" <?=$item['status']=="disabled"?"selected":""?>>Disabled</option>
									</select>
									<button data-nid="<?=$item['network_id']?>" class="btn btn-primary btn-sm btn-small statusupdate" style="height:20px !important;padding-top:0px;margin-left:10px;float:right" name="statusupdate">Apply</button>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
            </div>
		</div>
	</div>
</div>
</div>
