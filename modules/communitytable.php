<?php
$data = makeRequest('community/communityrequestlist', 'GET');
//echo "<xmp>".print_r($data,1)."</xmp>";
?>
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">
			<div class="grid-title no-border">
				<h4>Networks</h4>
			</div>
			<div class="grid-body no-border">
				<table class="table" id="membertable" >
					<thead>
						<tr>
							<th>Network</th>
							<th>Location</th>
							<th>Owner(s)</th>
							<th>Activity Log</th>
							<th style="width:250px">Status</th>
              <!--<th>Department</th>-->
						</tr>
					</thead>
					<tbody>
						<?php foreach($data['networks'] as $key => $array)
						{
							?>
							<tr class="odd gradeX">
								<td><?= $array['name'] ?></td>
								<td><?= $array['country'] ?></td>
								<td><?= $array['email'] ?></td>
								<td></td>
								<td style="width:200px">
									<select class="form-control statusselect" id='status_<?=$array['id']?>' style="height:20px !important;">
										<option>Select...</option>
										<option value="approve" <?=$array['status']=="active"?"selected":""?>>Active</option>
										<option value="pending" <?=$array['status']=="pending"?"selected":""?>>Pending</option>
										<option value="freeze" <?=$array['status']=="freeze"?"selected":""?>>Frozen</option>
										<option value="remove">Remove</option>
									</select>
									<button data-nid="<?=$array['id']?>" class="btn btn-primary btn-sm btn-small statusupdate" style="height:20px !important;padding-top:0px;margin-left:10px;" name="statusupdate">Apply</button>
								</td>
	              <!--<td>A&amp;E</th>-->
							</tr>
							<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
