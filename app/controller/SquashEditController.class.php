<?php
/**
* 
*/
class SquashEditController extends FController
{
	private $_squashResultRepo;
	
	public function __construct()
	{
		parent::__construct();
		$this->_squashResultRepo = new SquashResultRepository();
	}
	
	public function saveResult()
	{
		$playedAt = $_POST['date'];
		$playerOneId = $_POST['player_one_id'];
		$playerTwoId = $_POST['player_two_id'];
		
		$scoreOne = $_POST['score_one'];
		$scoreTwo = $_POST['score_two'];
		
		//tworzymy wyniki w obie strony
		$result1 = $this->_prepareResultObject($playerOneId, $playerTwoId, $playedAt, $scoreOne, $scoreTwo);
		$result2 = $this->_prepareResultObject($playerTwoId, $playerOneId, $playedAt, $scoreTwo, $scoreOne);
		
		try {
			$this->_squashResultRepo->save($result1);
			$this->_squashResultRepo->save($result2);
			$this->setPage('ajax/default');
			return FController::OK;
		} catch (Exception $e) {
			
			return FController::ERR;
		}
		
	}
	
	/**
	 * przygotowuje obiekt encji z wynikiem meczu - pobiera go z bazy (i aktualizuje pola wynikow) lub tworzy nowy
	 * 
	 * @param int $playerOneId
	 * @param int $playerTwoId
	 * @param string $playedAt
	 * @param int $scoreOne
	 * @param int $scoreTwo
	 * @return SquashResultEntity
	 */
	private function _prepareResultObject($playerOneId, $playerTwoId, $playedAt, $scoreOne, $scoreTwo)
	{
		$result = $this->_squashResultRepo->getOneBy(array('playerOneId' => $playerOneId, 'playerTwoId' => $playerTwoId, 'playedAt' => $playedAt));
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
		
		return $result;
	}
}





?>