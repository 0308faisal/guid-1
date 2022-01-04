<?php include('../functions.php') ?>
<?php
	if(isset($_GET['searchterm'])){
		$search=isset($_GET['searchterm'])?"&searchterm=".$_GET['searchterm']:"";
		$_SESSION['searchterm']=$_GET['searchterm'];
	}
	else{
		unset($_SESSION['searchterm']);
	}
?>
<?php include('inc/header.php') ?>




	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="page-title">
        	<h3>Guidelines</h3>
        	<span style='float:right'><input type='text' id='search' name='search' placeholder='Search...'><button id='searchbutton' value='Search' style='height:37px'>Search</button></span>
        </div>

        <?php include('modules/guidetable-frontend.php') ?>


	</div>


	<!--END PAGE CONTENT-->


<?php include('inc/scripts.php') ?>
<script src="assets/js/datatables.js" type="text/javascript"></script>
<script src="assets/js/pages/index.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
