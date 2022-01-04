<?php include('functions.php') ?>

<?php
if(isset($_POST['activenetwork'])){
  $_SESSION['activenetwork']=cleanInputs($_POST['activenetwork']);
  foreach($user->networks as $key=>$network){
    if($network['id']==$_SESSION['activenetwork']){
      $_SESSION['activenetworkname']=$network['name'];
    }
  }
}
else{
  if(count($user->networks)==1){
    $_SESSION['activenetwork']=$user->networks[0]['id'];
    $_SESSION['activenetworkname']=$user->networks[0]['name'];
  }
}
if(isset($_SESSION['activenetwork'])){
  foreach($user->networks as $key=>$network){
    if($network['id']==$_SESSION['activenetwork']){
      if($network['manager']=="false"){
        unset($_SESSION['activenetwork']);
        unset($_SESSION['activenetworkname']);
        header('location: /frontend/index.php');
      }
    }
  }
}
?>
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
        <h3>Guidelines</h3>
        <a href="guideline_edit.php" style="float:right" type="button" class="btn btn-primary">Create Guide</a>
        <span style='float:right'><input type='text' id='search' name='search' placeholder='Search...'><button id='searchbutton' value='Search' style='height:37px'>Search</button></span>

      </div>

    <?php include('modules/guidetable_manage.php') ?>
    <div class="addNewRow"></div>
  </div>
</div>
<!-- END PAGE -->
<div id="myModal" class="modal fade">
    <div class="modal-dialog" style="width:300px;height:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Select Network</h4>
            </div>
            <div class="modal-body">
              <form method="post">
               <select name="activenetwork">
                 <?php
                    foreach($user->networks as $key=>$network){
                      echo "<option value='{$network['id']}'>{$network['name']}</option>";
                    }
                 ?>
               </select>
               <input type="submit"> 
             </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER -->
<?php include('inc/scripts.php') ?>
<script src="assets/js/pages/index.js" type="text/javascript"></script>
<script src="assets/js/pages/networks.js" type="text/javascript"></script>
<?php
if(!isset($_SESSION['activenetwork'])){
	echo "<script>$('#myModal').modal({backdrop: 'static', keyboard: false});</script>";
}
?>
<?php include('inc/footer.php') ?>
