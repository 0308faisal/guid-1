<?php
$data = makeRequest('network/memberactivationlist?n2n='.$_SESSION['activenetwork'], 'GET');
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
								<td style="width:200px">
									<select class="form-control statusselect" style="height:20px !important;">
										<option value="activate">Activate</option>
										<option value="resend">Resend Activation</option>
										<option value="remove">Remove</option>
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
