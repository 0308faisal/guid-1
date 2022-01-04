<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta charset="utf-8" />
<title>GuideDoc Administration</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta content="" name="description" />
<meta content="" name="author" />
<!-- BEGIN PLUGIN CSS -->
<link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/jquery-slider/css/jquery.sidr.light.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/jquery-datatable/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/boostrap-checkbox/css/bootstrap-checkbox.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
<link rel="stylesheet" href="assets/plugins/jquery-ricksaw-chart/css/rickshaw.css" type="text/css" media="screen">
<link rel="stylesheet" href="assets/plugins/jquery-morris-chart/css/morris.css" type="text/css" media="screen">
<link href="assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" media="screen"/>
<link rel="stylesheet" href="assets/css/jquery-ui.css" />

<!-- END PLUGIN CSS -->
<!-- BEGIN CORE CSS FRAMEWORK -->
<link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css"/>

<!-- END CORE CSS FRAMEWORK -->

<!-- BEGIN CSS TEMPLATE -->
<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
<!-- END CSS TEMPLATE -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="">
  <input type="hidden" id="token" value="<?=$_COOKIE['token']; ?>">
  <input type="hidden" id="activenetwork" value="<?=$_SESSION['activenetwork']; ?>">
<!-- BEGIN HEADER -->
<div class="header navbar navbar-inverse ">
  <!-- BEGIN TOP NAVIGATION BAR -->
  <div class="navbar-inner">
    <div class="header-seperation">
      <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
        <li class="dropdown"> <a id="main-menu-toggle" href="#main-menu"  class="" >
          <div class="iconset top-menu-toggle-white"></div>
          </a> </li>
      </ul>
      <!-- BEGIN LOGO -->
      <a href="index.php"><img src="assets/img/logo.png" class="logo" alt=""  data-src="assets/img/logo.png" data-src-retina="assets/img/logo2x.png"/></a>
      <!-- END LOGO -->
      <ul style="list-style-type: none;margin-left:-30px">
			<li> <a href="frontend/">Standard View</a></li>
		</ul>
      <br />
    </div>
    <!-- END RESPONSIVE MENU TOGGLER -->
    <div class="header-quick-nav" >
      <div class="pull-left">
        <div class="chat-toggler">
          <div style="display: inline-block;float: left;line-height: 35px;margin-left:20px"><?php if (isset($_SESSION['activenetworkname']) && !empty($_SESSION['activenetworkname'])) {?><span class="bold">Active Network:</span><?=$_SESSION['activenetworkname']; ?><a href="#" style="margin-left:20px;float:right;color: #ffffff !important;"  type="button" class="btn btn-primary" onclick="$('#myModal').modal({backdrop: 'static', keyboard: false});return false;">Change</a><?php } ?></div>
        </div>
      </div>
      <div class="pull-right">
        <div class="chat-toggler">
			  <div class="user-details">
				      <div class="username"><a href="profile.php" id="my-task-list"><?=$GLOBALS['user']->firstname; ?> <span class="bold"><?=$GLOBALS['user']->lastname; ?></span> </a> </div>
			  </div>
			  <div class="profile-pic" style="margin-left:15px"> <img src="<?=$GLOBALS['user']->avatar; ?>"  alt="" data-src="<?=$GLOBALS['user']->avatar; ?>" width="35" height="35" /> </div>

			</div>
			<div style="float:left;margin-right:5px;"><p><a href="#" id="logout">(logout)</a></p></div>
		  </div>
      </div>
    </div>
    <!-- END TOP NAVIGATION MENU -->
  </div>
  <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->
