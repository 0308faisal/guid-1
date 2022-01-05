<?php
//date_default_timezone_set("Asia/Karachi");
require_once 'config.inc.php';
ini_set('display_errors', 1);
ini_set('log_errors',1);
ini_set('error_log',$_SERVER['DOCUMENT_ROOT'].'errors.txt');
error_reporting(E_ALL);

require_once 'classes/api.class.php';
require_once 'classes/jwt.class.php';
require_once 'classes/errorLogs.class.php';
header("Access-Control-Allow-Origin: ".BASE_URL);
$error = new ErrorLogs();
if(!array_key_exists($_SERVER['SERVER_NAME'],HOSTS)){
	$error->apiLogs('error', 'Add '.$_SERVER['SERVER_NAME'].' in host configuration.');
}
$serverconf = HOSTS[$_SERVER['SERVER_NAME']];

spl_autoload_register(function ($class)
{
	include 'classes/' . strtolower($class) . '.class.php';
});
try
{
	$auth = new Auth();
	$preset=array('api','auth','citation','comment','community','content','db','file','guideapi','guideline','jwt','log','network','user','errorLogs');
	$allowed=array_fill_keys($preset,array());
	$allowed['auth']=array("authenticate");
	$allowed['guideapi']=array("getdropdowndata");
	$allowed['network']=array("getnetworks");
	$allowed['user']=array("register","resetpassword","validatepassword","validateemail","changepassword");
	if(isset($allowed[$_REQUEST['class']]) && (!in_array(rtrim($_REQUEST['request'],"/"),$allowed[$_REQUEST['class']])) && !$auth->validatetoken())
	{
		$error->apiLogs('error', 'Only accepts Authenticated requests/ Auth token is not valid');
		throw new Exception('Only accepts Authenticated requests');
	}
	$Db = new Db(
			$serverconf['db_host'],
			$serverconf['db_user'],
			$serverconf['db_password'],
			$serverconf['db_name']
	);

	$directory = 'classes';
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));
	foreach($scanned_directory as $key => $file)
	{
		$tmpfile = explode(".", $file);
		$classes[$tmpfile[0]] = ucwords($tmpfile[0]);
	}
	if(array_key_exists($_REQUEST['class'], $classes))
	{
		$API = new $classes[$_REQUEST['class']]($_REQUEST['request']);
		echo stripcslashes(trim($API->processAPI(), '"'));
	}
	else
	{
		throw new Exception('Request Method not found');
	}
	$Db->close();
}
catch(Exception $e)
{
	header("HTTP/1.1 400 Bad Request");
	echo json_encode(Array('status' => 'Error', 'response' => $e->getMessage()));
}
