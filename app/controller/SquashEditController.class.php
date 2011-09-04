<?php
/**
* 
*/
class SquashEditController extends FController
{
	public function __construct()
	{
		parent::__construct();
		$this->_squashResultRepo = new SquashResultRepository();
		$this->_usersRepo = new FUserRepository();
	}
	
	public function saveResult()
	{
		$playedAt = $_POST['date'];
		$playerOneId = $_POST['player_one_id'];
		$playerTwoId = $_POST['player_two_id'];
		
		$scoreOne = $_POST['score_one'];
		$scoreTwo = $_POST['score_two'];
		
		$repo = new SquashResultRepository();
		$result = $repo->getOneBy(array('playerOneId' => $playerOneId, 'playerTwoId' => $playerTwoId, 'playedAt' => $playedAt));
		var_dump($result);
		
		
		if ($result) {
			$result->scoreOne = $scoreOne;
			$result->scoreTwo = $scoreTwo;
		} else {
			$data = array (
				'playedAt'		=>	$playedAt,
				'playerOneId'	=>	$playerOneId,
				'playerTwoId'	=>	$playerTwoId,
				'scoreOne'		=>	$scoreOne,
				'scoreTwo'		=>	$scoreTwo
			);
			$result = new SquashResultEntity($data);
		}
		
		$repo->save($result);
		$this->setPage('ajax/default');
		return FController::OK;
		
	}
}





?>