<?php
define("ROOT_DIR", "../..");
require_once ROOT_DIR . "/app/config/config.php";

$flite = FLite::getInstance();

//set_error_handler(array($flite, 'errorHandler'));
$flite->setMode(FLite::MODE_WWW);

ob_start();

$flite->go();

ob_end_flush();


?>
