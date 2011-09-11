<?php
define("ROOT_DIR", "../..");
require_once ROOT_DIR . "/app/config/config.php";

$flite = FLite::getInstance();
$flite->setMode(FLite::MODE_BATCH);

try {
	$users = importUsers();
	importResults($users);
} catch (FRepositoryException $e) {
	echo $e->getMessage();
}


function importResults($users)
{
	$file = fopen('Squash_new.csv', "r");
	$results = array();
	while (($row = fgetcsv($file)) !== FALSE) {
		$date		= $row[0];
		$playerOne	= $row[1];
		$playerTwo	= $row[2];
		$scoreOne	= $row[3];
		$scoreTwo	= $row[4];
		
		if (isset($results[$date][$playerOne][$playerTwo]['score_one'])) {
			$results[$date][$playerOne][$playerTwo]['score_one'] += $scoreOne;
			$results[$date][$playerOne][$playerTwo]['score_two'] += $scoreTwo;
		} else {
			$results[$date][$playerOne][$playerTwo]['score_one'] = $scoreOne;
			$results[$date][$playerOne][$playerTwo]['score_two'] = $scoreTwo;
		}
		
	}
	
	foreach ($results as $date => $result) {
		foreach ($result as $playerOneName => $games) {
			foreach ($games as $playerTwoName => $scores) {
				saveResult($users[$playerOneName], $users[$playerTwoName], $scores['score_one'], $scores['score_two'], $date);
			}
		}
	}
	
	
}

function saveResult($playerOne, $playerTwo, $scoreOne, $scoreTwo, $playedAt)
{
	//var_dump($playedAt);
	$playedAt = strtotime($playedAt);
	$playedAt = date("Y-m-d", $playedAt);
	//var_dump($playedAt);
	//return;
	
	$repo = new SquashResultRepository();
	
	$res1Arr = array (
		'playerOneId'	=>	$playerOne->id,
		'playerTwoId'	=>	$playerTwo->id,
		'scoreOne'		=>	$scoreOne,
		'scoreTwo'		=>	$scoreTwo,
		'playedAt'		=>	$playedAt
	);
	$res1 = new SquashResultEntity($res1Arr);
	$repo->save($res1);
	
	$res2Arr = array (
			'playerOneId'	=>	$playerTwo->id,
			'playerTwoId'	=>	$playerOne->id,
			'scoreOne'		=>	$scoreTwo,
			'scoreTwo'		=>	$scoreOne,
			'playedAt'		=>	$playedAt
	);
	$res2 = new SquashResultEntity($res2Arr);
	$repo->save($res2);
}


/**
 * wstawia do bazy dane userow
 * 
 * @return array
 */
function importUsers()
{
	$usersRepo = new FRepository('SquashPlayerEntity');
	
	
	$karolArr = array (
		'name'		=>	'Karol',
		'short_name'=>	'KT',
		'email'		=>	'karol@goldenline.pl',
		'pass'		=>	md5('1234')	
	);
	$karol = new SquashPlayerEntity($karolArr);
	$usersRepo->save($karol);
	
	$grzesiekArr = array (
			'name'		=>	'Grzesiek',
			'short_name'=>	'GK',
			'email'		=>	'grzegorz.krysiak@goldenline.pl',
			'pass'		=>	md5('1234')	
	);
	$grzesiek = new SquashPlayerEntity($grzesiekArr);
	$usersRepo->save($grzesiek);
	
	$jacekArr = array (
			'name'		=>	'Jacek',
			'short_name'=>	'JP',
			'email'		=>	'jacek.perkowski@goldenline.pl',
			'pass'		=>	md5('1234')	
	);
	$jacek = new SquashPlayerEntity($jacekArr);
	$usersRepo->save($jacek);
	
	$pakitaArr = array (
			'name'		=>	'Pakita',
			'short_name'=>	'PM',
			'email'		=>	'pakita.majewska@goldenline.pl',
			'pass'		=>	md5('1234')	
	);
	$pakita = new SquashPlayerEntity($pakitaArr);
	$usersRepo->save($pakita);
	
	$lucekArr = array (
			'name'		=>	'Lucek',
			'short_name'=>	'LS',
			'email'		=>	'lucjan.samulowski@goldenline.pl',
			'pass'		=>	md5('1234')	
	);
	$lucek = new SquashPlayerEntity($lucekArr);
	$usersRepo->save($lucek);
	
	$konradArr = array (
			'name'		=>	'Konrad',
			'short_name'=>	'KJ',
			'email'		=>	'konrad.jarowski@goldenline.pl',
			'pass'		=>	md5('1234')	
	);
	$konrad = new SquashPlayerEntity($konradArr);
	$usersRepo->save($konrad);
	
	$miloszArr = array (
				'name'		=>	'Miłosz',
				'short_name'=>	'MR',
				'email'		=>	'milosz.ryniecki@goldenline.pl',
				'pass'		=>	md5('1234')	
	);
	$milosz = new SquashPlayerEntity($miloszArr);
	$usersRepo->save($milosz);
	
	$kamilArr = array (
					'name'		=>	'Kamil',
					'short_name'=>	'K?',
					'email'		=>	'',
					'pass'		=>	md5('1234')	
	);
	$kamil = new SquashPlayerEntity($kamilArr);
	$usersRepo->save($kamil);
	
	$ret = array (
		'Karol'	=>	$karol,
		'Gelu'	=>	$grzesiek,
		'Pakita'=>	$pakita,
		'Lucek'	=>	$lucek,
		'Konrad'=>	$konrad,
		'Miłosz'=>	$milosz,
		'Jacek'	=>	$jacek,
		'Kamil'	=>	$kamil
	);
	
	return $ret;
	
	
	
	
}
 