<?php declare(strict_types=1);
include '../functions.php'; ?>
<?php include 'inc/header.php'; ?>

<input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
<div class="alert alert-success fade in" id="resultmsg" style="display:none">

</div>

	<!--PAGE CONTENT-->

	<div class="container-fluid">
		<div class="page-title">
        	<h3>Network</h3>
        </div>

        <?php include 'modules/networktable-frontend.php'; ?>


	</div>







	<!--END PAGE CONTENT-->


<?php include 'inc/scripts.php'; ?>
<script src="assets/js/pages/networks.js" type="text/javascript"></script>
<?php include 'inc/footer.php'; ?>
