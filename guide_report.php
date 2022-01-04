<?php include('inc/header.php') ?>

<!-- BEGIN CONTAINER -->
<div class="page-container row-fluid">

<?php include('inc/sidebar.php') ?>


  <!-- BEGIN PAGE CONTAINER-->
  <div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <!-- <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="#" class="active">Tables</a> </li>
      </ul> -->
      <div class="page-title">
        <h3>Alcohol Detoxification Report</h3>
      </div>
  
    <?php include('modules/guide_report_table.php') ?>
    </div>

  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<?php include('inc/footer.php') ?>
