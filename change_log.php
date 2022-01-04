<?php include('functions.php') ?>
<?php include('inc/header.php') ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include('inc/sidebar.php') ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="clearfix"></div>
    <div class="content">
      <!-- <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="#" class="active">Tables</a> </li>
      </ul> -->
      <div class="page-title">
        <h3>Change Log</h3>
      </div>

    <?php include('modules/logtable.php') ?>

  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<?php include('inc/footer.php') ?>
