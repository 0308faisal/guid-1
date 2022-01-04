<?php
require($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
$openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'].'/rest_v2/']);
header('Content-Type: application/json');
echo $openapi->toJSON();
?>