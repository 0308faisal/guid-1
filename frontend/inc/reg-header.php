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
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <style>
	  .container-fluid{
			margin-top:100px;
		}
  </style>
  </head>
  <body class="home">
  <div class="header navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-header">
     <a href="index.php" class="pull-left"><img src="../assets/img/logo.png" class="logo" alt="" data-src="../assets/img/logo.png" data-src-retina="../assets/img/logo2x.png"></a>
    </div>

  	  <div class="login-widget col-md-6 pull-right" style="padding:10px 0 0 0;">
      <div class="input-group">
		  <div>
          <form name="loginform" id="loginform" method="post" action="<?=BASE_URL?>frontend/register.php">
			  <input type="text" class="form-control" id ="login_email" name="email" placeholder="Email" style="width:200px; float:left; margin-right:5px;">
			  <input type="password" class="form-control" id="login_password" name="password" autocomplete="off" placeholder="Password" style="width:200px; float:left; margin-right:5px;">
			  <input type="submit" class="btn btn-primary" value="Login">
          </form>
          </div>
          <a href="#" id="resetpassword" style="float:left">Reset Password</a>
      </div>

      </div>


  </div>
