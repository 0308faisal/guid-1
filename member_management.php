<?php declare(strict_types=1);
include 'functions.php'; ?>
<?php include 'inc/header.php'; ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">
<input type="hidden" id="token" name="token" value="<?=$_COOKIE['token']; ?>">
<?php include 'inc/sidebar.php'; ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="clearfix"></div>
    <div class="content">
      <div class="page-title">
          <h3>Signups</h3>
      </div>
  
    <?php include 'modules/signuptable.php'; ?>
    <div class="addNewRow"></div>
  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include 'inc/scripts.php'; ?>
<script src="assets/js/pages/signups.js" type="text/javascript"></script>
<?php include 'inc/footer.php'; ?>
