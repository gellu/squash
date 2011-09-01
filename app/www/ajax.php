<?php
define("ROOT_DIR", "../..");
require_once ROOT_DIR . "/app/config/config.php";

$flite = FLite::getInstance();
$flite->setMode(FLite::MODE_AJAX_JSON);

ob_start();

$flite->go();

ob_end_flush();


?>
