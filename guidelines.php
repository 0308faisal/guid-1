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
        <h3>Admin Dashboard</h3>
        <a href="guideline_edit.php" style="float:right" type="button" class="btn btn-primary">Create Guide</a>
        <span style='float:right'><input type='text' id='search' name='search' placeholder='Search...'><button id='searchbutton' value='Search' style='height:37px'>Search</button></span>

      </div>

    <?php include('modules/guidetable_browse.php') ?>
    <div class="addNewRow"></div>
  </div>
</div>
<!-- END PAGE -->

<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/index.js" type="text/javascript"></script>
<?php include('inc/footer.php') ?>
