<?php
		$data = makeRequest("network/networkinvites/".$_GET['id'],"GET");
		foreach($data["networkinvites"] as $key=>$array){
			$bounce=makeMailgunRequest("/bounces/".$array['email']);
			if($bounce['bounce']['email']==$array['email']){
				$data['networkinvites'][$key]['status']="Bounced";
			}
			elseif($array['signedup']=="1"){
				$data['networkinvites'][$key]['status']="Awaiting Validation";
			}
		}
		$invites=makeMailgunRequest("/stats");
		$sent=$delivered=$bounced=0;
		foreach($invites['items'] as $key=>$val){
			if($val['event']=="sent"){
				$sent+=$val['total_count'];
			}
			elseif($val['event']=="delivered"){
				$delivered+=$val['total_count'];
			}
			elseif($val['event']=="bounced"){
				$bounced+=$val['total_count'];
			}
		}

?>
<div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <div class="grid-title no-border">
              <h4>Network Invites</h4>
              <div>
			  <strong>Sent:</strong> <?=$sent?><br />
			  <strong>Delivered:</strong> <?=$delivered?><br />
			  <strong>Bounced:</strong> <?=$bounced?>
			  </div>
            </div>
            <div class="grid-body no-border">
              <table class="table" id="invitetable" >
                <thead>
                  <tr>
                    <th>Invite ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <!--<th>Department</th>-->

                  </tr>
                </thead>
                <tbody>
				  <?php foreach($data["networkinvites"] as $key=>$array){?>
                  <tr class="odd gradeX">
                    <td><?=$array['invite_id']?></td>
                    <td><?=$array['email']?></th>
                    <td><?=$array['status']?></th>
                    <!--<td>A&amp;E</th>-->
                  </tr>
                  <?php } ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
