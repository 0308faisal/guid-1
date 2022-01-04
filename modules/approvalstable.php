<?php
$data = makeRequest('user/approvallist/?n2n='.$_SESSION['activenetwork'], 'GET');
?>
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">
			<div class="grid-title no-border">
				<h4>Members</h4>
			</div>
			<div class="grid-body no-border">
				<table class="table" id="membertable" >
					<thead>
						<tr>
							<th>ID</th>
							<th>Network</th>
							<th>Email</th>
							<th>Name</th>
							<th>Status</th>
							<th style="width:250px">Action</th>
              <!--<th>Department</th>-->
						</tr>
					</thead>
					<tbody>
						<?php foreach($data['members'] as $key => $array)
						{
							?>
							<tr class="odd gradeX">
								<td class="idfield" data-activation_code="<?= $array['activation_code'] ?>"><?= $array['id'] ?></th>
								<td><?= $array['network'] ?></th>
								<td><?= $array['email'] ?></th>
								<td><?= $array['fname'] ?> <?= $array['lname'] ?></td>
								<td><?= $array['manager']==1?"Manager":$array['status']?></td>
								<td style="width:200px">
									<select class="form-control statusselect" style="height:20px !important;">
										<option value="">Select an option</option>
										<option value="manager|<?=$_SESSION['activenetwork']?>">Grant Manager Access</option>
										<option value="active|<?=$_SESSION['activenetwork']?>">Grant Standard Access</option>
										<option <?=$array['status']!="pending"||$array['manager']==1?"selected":""?> value="remove|<?=$_SESSION['activenetwork']?>">Remove</option>
									</select>
									<button class="btn btn-primary btn-sm btn-small statusupdate" style="height:20px !important;padding-top:0px;margin-left:10px;" name="statusupdate">Apply</button></td>
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
