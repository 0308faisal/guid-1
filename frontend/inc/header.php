<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My GuideDoc Portal</title>

    <!-- Bootstrap -->
    <link href="<?=BASE_URL?>assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>assets/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>frontend/assets/css/front-end.css" rel="stylesheet" type="text/css"/>
	<link href="<?=BASE_URL?>assets/css/custom-icon-set.css" rel="stylesheet" type="text/css"/>
  <!-- <link href="../assets/css/spacetree-base.css" rel="stylesheet" type="text/css"/> -->
  <link href="<?=BASE_URL?>assets/css/spacetree.css" rel="stylesheet" type="text/css"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  <!--[if IE]><script language="javascript" type="text/javascript" src="assets/js/excanvas.js"></script><![endif]-->
  </head>
  <body class="home">
  <div class="header navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
     <a href="index.php<?php if (isset($_SESSION['searchterm'])) {
    echo '?searchterm='.$_SESSION['searchterm'];
}?>" class="pull-left"><img src="../assets/img/logo.png" class="logo" alt="" data-src="../assets/img/logo.png" data-src-retina="../assets/img/logo2x.png"></a>
    </div>


      <nav class="collapse navbar-collapse bs-navbar-collapse">
      <ul class="nav nav-pills pull-left main-navigation">
	  	<li class="<?=basename($_SERVER['PHP_SELF'])=="index.php"?"active":""?>"><a href="index.php<?php if (isset($_SESSION['searchterm'])) {
    echo '?searchterm='.$_SESSION['searchterm'];
}?>" id="navguide">Guidelines</a></li>
      <?php
      if($user->iplogin!=true){?>
        <li class="<?=basename($_SERVER['PHP_SELF'])=="networks.php"?"active":""?>"><a href="networks.php" id="navnetwork">Networks</a></li>
      <?php } ?>

	  	<?php
        if (($user->manager >0 || $user->admin>0)) {
            ?>
		<li><a href="<?=BASE_URL?>index.php" id="navdash">Admin Area</a></li>
		<?php
        } ?>

	  </ul>
	  <div class="pull-right">
		<div class="chat-toggler-black">
			  <div class="user-details">
				<div class="username"><a href="profile.php" id="my-task-list"><?=$user->firstname?> <span class="bold"><?=$user->lastname?></span> </a> </div>
			  </div>
			  <div class="profile-pic" style="margin-left:15px"> <img src="<?=$user->avatar?>"  alt="" data-src="<?=$user->avatar?>" width="35" height="35" /> </div>

			</div>
      <div style="float:left;margin-right:5px;"><p><a style="color:#fff" href="/frontend/profile.php" id="profile">My Profile</a></p></div>
			<div style="float:left;margin-right:5px;"><p><a style="color:#fff" href="#" id="logout">(logout)</a></p></div>
		  </div>
	  </nav>

  </div>

