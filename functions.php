<?php
require_once 'config.inc.php';
error_reporting(E_ERROR);
ini_set('display_errors', DEBUG);
date_default_timezone_set('Europe/Dublin');
$serverconf = HOSTS[$_SERVER['SERVER_NAME']];
if(isset($serverconf['captcha_ignore_ip']) && in_array($_SERVER['REMOTE_ADDR'],$serverconf['captcha_ignore_ip'])){
	$ignore_captcha=true;
}
else {
	$ignore_captcha=false;
}
session_start();


if(isset($_GET["logout"])
|| ((!isset($_COOKIE['token'])
&& strpos($_SERVER["REQUEST_URI"], "rest_v2") == false
&& strpos($_SERVER["REQUEST_URI"], "register.php") == false
&& strpos($_SERVER["REQUEST_URI"], "pending.php") == false
 && strpos($_SERVER["REQUEST_URI"], "changepassword.php") == false
 && strpos($_SERVER["REQUEST_URI"], "download.php") == false
 && strpos($_SERVER["REQUEST_URI"], "network_create.php") == false)))
{
	setcookie("token", "", time() - 9600, "/");
	unset($_COOKIE['token']);
	session_destroy();
	header("location: " . BASE_URL . "frontend/register.php");
}

if(!isset($_COOKIE['token']) && strpos($_SERVER["REQUEST_URI"], "register.php") == false){
	$data = makeRequest("auth/authenticate/?ip=".getip(), "POST");
}

if(isset($_COOKIE['token']))
{
	$data = makeRequest("user/getuser", "GET");
	if(is_array($data) && isset($data["user"]))
	{
		$user = new stdClass();
		$user->id = $data["user"];
		$user->firstname = $data["firstname"];
		$user->lastname = $data["lastname"];
		$user->occupation = $data["occupation"];
		$user->specialty = $data["specialty"];
		$user->gradename = $data["gradename"];
		$user->email = $data["email"];
		$user->employer = $data["employer"];
		$user->networks = $data["networks"];
		foreach($user->networks as $key=>$network){
			if($network['manager']=="true"){
				$user->manager=1;
			}
		}
		$user->avatar = $data["avatar"];
		$user->admin = $data["admin"];
		$user->iplogin = $data["iplogin"];
		$user->profileaccess = $data["profileaccess"];
	}
}

if(isset($_COOKIE['token'])
&& strpos($_SERVER["REQUEST_URI"], "frontend") == false
&& ($user->manager < 1 && $user->admin < 1))
{
	header("location: " . BASE_URL . "frontend/index.php");
}

function makeRequest($data, $method)
{
	
	if($method == "GET")
	{
		$url = SITE_URL . $data;
	}
	else
	{
		$tmp = explode("?", $data);
		$url = SITE_URL . $tmp[0];
		parse_str(urldecode($tmp[1]), $data);
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if($method == "GET")
	{
		curl_setopt($ch, CURLOPT_POST, 0);
	}
	else
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: '.$_COOKIE['token']));
	$result = curl_exec($ch);
	$data=json_decode($result);
	if(isset($data->token) && $data->token!='')
	{
		setcookie("token", $data->token, time() + 14400, "/");
	}
	curl_close($ch);
	return json_decode($result, true);
}

function makeMailgunRequest($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, MAILGUN_URL . $url);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "api:" . MAILGUN_API);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function getip()
{
	if(isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
	{
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	$client = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote = $_SERVER['REMOTE_ADDR'];

	if(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	elseif(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
	else
	{
		$ip = $remote;
	}

	return $ip;
}

function cleanInputs($string) {
	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
 
	return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
 }

?>
