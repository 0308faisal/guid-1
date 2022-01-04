<?php include('../functions.php') ?>
<?php
$network = makeRequest("network/getnetworks","GET");
foreach($network["networks"] as $key=>$value){
  if($value['id']==$_GET['nid']){$network=$value['name'];break;}
}

?>
<?php include('inc/reg-header.php') ?>




	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="page-title">
        	<h3>Thank you</h3>
        </div>

        Thank you, your email address has been validated on our system and your request to access the GuideDoc Platform has been passed to the GuideDoc administration team.


	</div>







	<!--END PAGE CONTENT-->


<?php include('inc/scripts.php') ?>
<script src="assets/js/datatables.js" type="text/javascript"></script>
<script src="assets/js/pages/index.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
