<?php
$data = makeRequest('log/getlog', 'GET');

?>
<div class="row-fluid">
	<div class="span12">
		<div class="grid simple ">
			<div class="grid-title no-border">
				<h4>Changes</h4>
			</div>
			<div class="grid-body no-border">
				<table class="table" id="membertable" >
					<thead>
						<tr>
							<th>Date</th>
							<th>Guideline ID</th>
							<th>Email</th>
							<th>Change</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($data as $key => $array)
						{
							?>
							<tr class="odd gradeX">
								<td><?= $array['mdate'] ?></th>
								<td><?= $array['guideline_id'] ?></th>
								<td><?= $array['email'] ?></th>
								<td><?= $array['comment'] ?></td>
							</tr>
							<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
