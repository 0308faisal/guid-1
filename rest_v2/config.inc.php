<?php

define('DEBUG', 0);
define('JWTSECRET', getenv("JWTSECRET"));
define('GUIDEDOC_EMAIL', getenv("GUIDEDOC_EMAIL"));
define('MAILGUN_URL', getenv("MAILGUN_URL"));
define('MAILGUN_API', getenv("MAILGUN_API"));
define('FILESTORAGE', 'db'); //db,file
define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/rest_v2/');
define('BASE_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/');
define('DATA_DIR', '');
define('HOSTS', array(
	'guidedoc.test' => array(
		'captcha_secret' => getenv("CAPTCHA_SECRET"),
		'captcha_key' => getenv("CAPTCHA_KEY"),
		'mixpanel_id' => getenv("MIXPANEL_ID"),
		'db_host' => getenv("DB_HOST"),
		'db_port' => getenv("DB_PORT"),
		'db_user' => getenv("DB_USERNAME"),
		'db_password' => getenv("DB_PASSWORD"),
		'db_name' => getenv("DB_NAME")
	),
	'192.168.20.233' => array(
		'captcha_secret' => '6LfLgU0UAAAAAPZphjc_jkHpUFPVoMNgo2eKCCff',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => '138.68.131.29',
		'db_port' => 25060,
		'db_user' => 'doadmin',
		'db_password' => 'jx1cUnMo0NcC9uwc',
		'db_name' => 'Guidedoc'
	),
	'guidedoc.local' => array(
		'captcha_secret' => '6LfLgU0UAAAAAPZphjc_jkHpUFPVoMNgo2eKCCff',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => 'root',
		'db_name' => 'Guidedoc'
	),
	'dev.guidedoc.co' => array(
		'captcha_secret' => '6LcHyBcTAAAAAOaeIMEXrT_i8w1m4Q2q0yaih-AH',
		'captcha_key' => '6LcHyBcTAAAAACm1ZU3CC4XibdmO1Xvjz_GQ2tIC',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => '5c0b88f756b7c6b13f6a633fa83d975bc26628488a292b5d',
		'db_name' => 'Guidedoc'
	),
	'members.guidedoc.co' => array(
		'captcha_secret' => '6LcgJVAUAAAAANLHtTnClMOyeCS5qE954QqatDW8',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => 'c16c3f8b6312e47f7b6d605110d8120fcd2f39b3bb12068b',
		'db_name' => 'Guidedoc'
	),
	'my.guidedoc.co' => array(
		'captcha_secret' => '6LcHyBcTAAAAAOaeIMEXrT_i8w1m4Q2q0yaih-AH',
		'captcha_key' => '6LcHyBcTAAAAACm1ZU3CC4XibdmO1Xvjz_GQ2tIC',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => 'c16c3f8b6312e47f7b6d605110d8120fcd2f39b3bb12068b',
		'db_name' => 'Guidedoc'
	),
	'staging.guidedoc.co' => array(
		'captcha_secret' => '6LcHyBcTAAAAAOaeIMEXrT_i8w1m4Q2q0yaih-AH',
		'captcha_key' => '6LcHyBcTAAAAACm1ZU3CC4XibdmO1Xvjz_GQ2tIC',
		'mixpanel_id' => '2844cc41438a38ba4a13fc82d27978eb',
		'db_host' => 'localhost',
		'db_port' => 3306,
		'db_user' => 'root',
		'db_password' => '4210d63e1d6b18002524f7d16a0bd7701d1492e4cb2d7bb3',//'4210d63e1d6b18002524f7d16a0bd7701d1492e4cb2d7bb3',
		'db_name' => 'Guidedoc'
	)
		)
);
