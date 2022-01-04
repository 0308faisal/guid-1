<?php

declare(strict_types=1);

\error_reporting(\E_ERROR);
\ini_set('display_errors', DEBUG);
\date_default_timezone_set('Europe/Dublin');
\define('GUIDEDOC_EMAIL', 'hello@guidedoc.co');
\define('SALT', 'SALT');
//define('MAILGUN_URL', 'https://api.mailgun.net/v2/sandbox62128.mailgun.org');
\define('MAILGUN_URL', 'https://api.mailgun.net/v3/guidedoc.co');
\define('MAILGUN_API', 'key-3wmbeb1cgxkpg71tjc5k5yjybkc9eoz0');
\define('FILESTORAGE', 'db'); //db,file

if ($_SERVER['SERVER_NAME'] == 'staging.guidedoc.co') {
	\define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/rest/');
	\define('BASE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/');
	\define('CAPTCHA_SECRET', '6LfLgU0UAAAAAPZphjc_jkHpUFPVoMNgo2eKCCff');
	\define('MIXPANEL_ID', '2844cc41438a38ba4a13fc82d27978eb');
	\define('DB_HOST', '167.99.86.24');
	\define('DB_PORT', 3306);
	\define('DB_USER', 'root');
	\define('DB_PASSWORD', 'c16c3f8b6312e47f7b6d605110d8120fcd2f39b3bb12068b'); //4210d63e1d6b18002524f7d16a0bd7701d1492e4cb2d7bb3
	\define('DB_NAME', 'Guidedoc');
	\define('DATA_DIR', '');
} elseif ($_SERVER['SERVER_NAME'] == 'guidedoc.co') {
	\header('HTTP/1.1 301 Moved Permanently');
	\header('Location: https://my.guidedoc.co');
	exit();
} elseif ($_SERVER['SERVER_NAME'] == 'members.guidedoc.co') {
	\define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/rest/');
	\define('BASE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/');
	\define('CAPTCHA_SECRET', '6LcgJVAUAAAAANLHtTnClMOyeCS5qE954QqatDW8');
	\define('MIXPANEL_ID', '2844cc41438a38ba4a13fc82d27978eb');
	\define('DB_HOST', 'localhost');
	\define('DB_PORT', 3306);
	\define('DB_USER', 'root');
	\define('DB_PASSWORD', 'c16c3f8b6312e47f7b6d605110d8120fcd2f39b3bb12068b');
	\define('DB_NAME', 'Guidedoc');
	\define('DATA_DIR', '');
} elseif ($_SERVER['SERVER_NAME'] == 'my.guidedoc.co') {
	\define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/rest/');
	\define('BASE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/');
	\define('CAPTCHA_SECRET', '6LcHyBcTAAAAACm1ZU3CC4XibdmO1Xvjz_GQ2tIC');
	\define('MIXPANEL_ID', '2844cc41438a38ba4a13fc82d27978eb');
	\define('DB_HOST', 'localhost');
	\define('DB_PORT', 3306);
	\define('DB_USER', 'root');
	\define('DB_PASSWORD', 'c16c3f8b6312e47f7b6d605110d8120fcd2f39b3bb12068b');
	\define('DB_NAME', 'Guidedoc');
	\define('DATA_DIR', '');
} elseif ($_SERVER['SERVER_NAME'] == 'dev.guidedoc.co') {
	\define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/rest/');
	\define('BASE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/');
	\define('CAPTCHA_SECRET', '6LfLgU0UAAAAAPZphjc_jkHpUFPVoMNgo2eKCCff');
	\define('MIXPANEL_ID', '2844cc41438a38ba4a13fc82d27978eb');
	\define('DB_HOST', 'localhost');
	\define('DB_PORT', 3306);
	\define('DB_USER', 'root');
	\define('DB_PASSWORD', '5c0b88f756b7c6b13f6a633fa83d975bc26628488a292b5d');
	\define('DB_NAME', 'Guidedoc');
	\define('DATA_DIR', '');
}
//include_once('sessions/SessionHandler.php');

//$session = new MySessionHandler($sessiondb);
//session_set_save_handler($session, true);
\session_start();
$_SESSION['iplogin'] = false;

if (!isset($_SESSION['token']) && \strpos($_SERVER['REQUEST_URI'], 'rest') == false && \strpos($_SERVER['REQUEST_URI'], 'register.php') == false) {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$ip = \explode(',', $ip);
	$token = \base64_encode(genToken());
	$logindata = makeRequest('authenticateiplogin?iptoken=' . $token . '&ip=' . $ip[0], 'GET');

	if ($logindata['status'] == 'success') {
		$_SESSION['token'] = $logindata['token'];
		$_SESSION['iplogin'] = true;
	}
}

if (isset($_GET['logout'])) {
	\session_destroy();
	unset($_SESSION['token']);
	\header('location: ' . BASE_URL . 'frontend/register.php');
}

if ((!isset($_SESSION['token']) && \strpos($_SERVER['REQUEST_URI'], 'rest') == false && \strpos($_SERVER['REQUEST_URI'], 'register.php') == false && \strpos($_SERVER['REQUEST_URI'], 'changepassword.php') == false && \strpos($_SERVER['REQUEST_URI'], 'download.php') == false)) {
	\session_destroy();
	unset($_SESSION['token']);
	\header('location: ' . BASE_URL . 'frontend/register.php');
}

if (isset($_SESSION['token'])) {
	$data = makeRequest('user/?token=' . $_COOKIE['token'], 'GET');

	if (\is_array($data) && isset($data['user'])) {
		$user = new stdClass();
		$user->id = $data['user'];
		$user->firstname = $data['firstname'];
		$user->lastname = $data['lastname'];
		$user->occupation = $data['occupation'];
		$user->specialty = $data['specialty'];
		$user->gradename = $data['gradename'];
		$user->email = $data['email'];
		$user->employer = $data['employer'];
		$user->networks = $data['networks'];
		$user->avatar = $data['avatar'];
		$user->admin = $data['admin'];
	}
}

if (isset($_SESSION['token']) && \strpos($_SERVER['REQUEST_URI'], 'frontend') == false && $user->admin !== '1') {
	\header('location: ' . BASE_URL . 'frontend/index.php');
}

function makeRequest($data, $method)
{
	if ($method == 'GET') {
		$url = SITE_URL . $data;
	} else {
		$tmp = \explode('?', $data);
		$url = SITE_URL . $tmp[0];
		\parse_str(\urldecode($tmp[1]), $data);
	}
	$ch = \curl_init();
	\curl_setopt($ch, \CURLOPT_URL, $url);

	if ($method == 'GET') {
		\curl_setopt($ch, \CURLOPT_POST, 0);
	} else {
		\curl_setopt($ch, \CURLOPT_POST, 1);
		\curl_setopt($ch, \CURLOPT_POSTFIELDS, $data);
	}
	\curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);

	$server_output = \curl_exec($ch);

	if ($_GET['testmode'] == '1') {
		\print_r($server_output);
		die;
	}
	\curl_close($ch);
	return \json_decode($server_output, true);
}

function makeMailgunRequest($url)
{
	$ch = \curl_init();
	\curl_setopt($ch, \CURLOPT_URL, MAILGUN_URL . $url);
	\curl_setopt($ch, \CURLOPT_HTTPAUTH, \CURLAUTH_BASIC);
	\curl_setopt($ch, \CURLOPT_USERPWD, 'api:' . MAILGUN_API);
	\curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
	\curl_setopt($ch, \CURLOPT_CONNECTTIMEOUT, 10);
	\curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, 0);
	\curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, 0);
	$result = \curl_exec($ch);
	\curl_close($ch);
	return \json_decode($result, true);
}

function genToken()
{
	$number_of_groups = 5;
	$sum_to = 100;

	$groups = [];
	$group = 0;

	while (\array_sum($groups) !== $sum_to) {
		$groups[$group] = \mt_rand(0, $sum_to / \mt_rand(1, 5));

		if (++$group == $number_of_groups) {
			$group = 0;
		}
	}
	return \implode('|', $groups);
}
