<?php include('functions.php') ?>
<?php include('inc/header.php') ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include('inc/sidebar.php') ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
      <!-- <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="#" class="active">Tables</a> </li>
      </ul> -->
      <div class="page-title">
        <h3>Networks</h3>
      </div>

    <?php include('modules/networktable.php') ?>

  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/networks.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
