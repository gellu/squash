<?php
define("ROOT_DIR", "../..");
require_once ROOT_DIR . "/app/config/config.php";

$flite = FLite::getInstance();
$flite->setMode(FLite::MODE_BATCH);

$rankingRepo = new SquashRankingStateRepository();
$resultsRepo = new SquashResultRepository();
$playersRepo = new FRepository('SquashPlayerEntity');

$rankingBuilder = new SquashRankingBuilder($rankingRepo, $resultsRepo, $playersRepo);
$rankingBuilder->computeAll();
/*

$dates = $resultsRepo->getAllDates();

foreach ($dates as $date)
{
	$resultsForDate = 
}

*/