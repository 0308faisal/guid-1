<?php
require_once 'functions.php';
require_once 'db.inc.php';
require_once 'guideapi.class.php';
$Db = new Db;
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new GuideAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo stripcslashes(trim($API->processAPI(),'"'));
} catch (Exception $e) {
    echo json_encode(Array('status'=>'Error','response' => $e->getMessage()));
}
$Db->close();
