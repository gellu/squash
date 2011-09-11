<?php
define("ROOT_DIR", "../..");
require_once ROOT_DIR . "/app/config/config.php";

$flite = FLite::getInstance();
$flite->setMode(FLite::MODE_BATCH);

$rankingRepo = new SquashRankingStateRepository();
$resultsRepo = new SquashResultRepository();

$rankingBuilder = new SquashRankingBuilder($rankingRepo, $resultsRepo);
$rankingBuilder->computeAll();
/*

$dates = $resultsRepo->getAllDates();

foreach ($dates as $date)
{
	$resultsForDate = 
}

*/