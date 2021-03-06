<?php
/**
 * sluzy do wyliczania rankingu gracza squasha
 * 
 * @author karol
 *
 */
class SquashRankingBuilder
{
	/**
	 * startowa wartosc rankingu dla gracza
	 * 
	 * @var unknown_type
	 */
	const STARTING_RANKING = 1200;
	
	/**
	 * repo do pobierania wynikow
	 * 
	 * @var SquashResultRepository
	 */
	private $_resultsRepo;
	
	/**
	 * repo do rankingow
	 * 
	 * @var SquashRankingStateRepository
	 */
	private $_rankingRepo;
	
	/**
	 * repo do operacji na graczach
	 * 
	 * @var FRepository
	 */
	private $_playersRepo;
	
	/**
	 * konstuktor
	 * 
	 * @param SquashRankingStateRepository $rankingRepo
	 * @param SquashResultRepository $resultsRepo
	 * @param FRepository $playersRepo
	 */
	
	public function __construct(SquashRankingStateRepository $rankingRepo, SquashResultRepository $resultsRepo, FRepository $playersRepo)
	{
		$this->_rankingRepo = $rankingRepo;
		$this->_resultsRepo = $resultsRepo;
		$this->_playersRepo = $playersRepo;
		
	}
	
	/**
	 * wylicza rankingi na poszczegolne dni dla wszystkich graczy i aktualizuje ich aktualny ranking
	 * 
	 * @return void
	 */
	public function computeAll()
	{
		$dates = $this->_resultsRepo->getAllDates();
		foreach ($dates as $date) {
			echo "\ncomputing for date ". $date;
			$this->computeForDate($date);
		}
		//aktualizacja rankingow graczy
		$this->_saveRankingForPlayers();
		
	}
	
	/**
	 * wylicza ranking biorac pod uwage mecze rozegrane danego dnia i dotychczasowy ranking
	 * 
	 * @param string $date
	 * @return void
	 */
	public function computeForDate($date)
	{
		$rankingData = $this->_getRankingDataBeforeDate($date);
		$results	 = $this->_resultsRepo->getAllBy(array('playedAt' => $date));
		
		$rankingChanges = $this->_computeRankingChanges($results, $rankingData);
		//var_dump($rankingData, $rankingChanges);
		foreach ($rankingChanges as $playerId => $rankingChange)
		{
			$oldRanking = isset($rankingData[$playerId]) ? $rankingData[$playerId]->ranking : self::STARTING_RANKING;
			$newRanking = $oldRanking + $rankingChange;
			$rankingStateData = array (
				'player_id'	=>	$playerId,
				'validFor'	=>	$date,
				'ranking'	=>	$newRanking
			);
			$rankingState = new SquashRankingStateEntity($rankingStateData);
			$this->_rankingRepo->save($rankingState);
		}
	}
	
	/**
	 * wylicza zmiany rankingu na podstawie wynikow z danego dnia i rankingu na poczatku danego dnia
	 * 
	 * @param array $results tablica SquashResultEntity
	 * @param array $rankingData tablica SquashRankingStateEntity
	 * @return array
	 */
	private function _computeRankingChanges(array $results, array $rankingData)
	{
		$rankingChanges = array();
		foreach ($results as $result) {
			$playerOneChange = $this->_computeRankingChangeForResult($result, $rankingData);
			if (isset($rankingChanges[$result->playerOneId])) {
				$rankingChanges[$result->playerOneId] += $playerOneChange;
			} else {
				$rankingChanges[$result->playerOneId] = $playerOneChange;
			}
		}
		
		return $rankingChanges;
	}
	
	/**
	 * wylicza zmiany rankingu dla gracza podanego w $result jako $playerOneId. 
	 * Zmiana dla drugiego gracza wyliczana jest przy przetwarzaniu drugiego, odpowiadajacego obecnemu, ResultEntity (zapis w dwie strony) 
	 * wyliczanie rankingu bazuje na metodzie stosowanej na kurniku (http://www.kurnik.pl/ranking.phtml)
	 * 
	 * @param SquashResultEntity $result
	 * @param array $rankingData
	 * @return
	 */
	private function _computeRankingChangeForResult(SquashResultEntity $result, array $rankingData)
	{
		$playerOneRanking = isset($rankingData[$result->playerOneId]) ? $rankingData[$result->playerOneId]->ranking : self::STARTING_RANKING;
		$playerTwoRanking = isset($rankingData[$result->playerTwoId]) ? $rankingData[$result->playerTwoId]->ranking : self::STARTING_RANKING;
		
		//stosowane ponizej magiczne liczby 400 i 32 sa rownie magiczne w zapisie rankingu na kkurniku
		//nie wiem co znacza, wiec nie wynosze ich do stalych klasy
		$rankingChange = 0;
		$d	= $playerTwoRanking - $playerOneRanking;
		$we	= 1/(1 + pow(10,($d/400)));
		//zwyciestwa
		$wy		= 1;
		$diff	= $wy - $we;
		$rankingChange = $result->scoreOne * ($diff * 32);
		
		$wy		= 0;
		$diff	= $wy - $we;
		$rankingChange += $result->scoreTwo * ($diff * 32);
		
		return $rankingChange; 
	}
	
	/**
	 * zwraca dane o rankingu userow w ostatniej chwili przed podana data (na poczatek podanego dnia)
	 * zwracana jest tablica zaindeksowana wg playerId, lub pusta tablica, jesli nie ma danych
	 * 
	 * @param string $date
	 * @return array
	 */
	private function _getRankingDataBeforeDate($date)
	{
		$rankingData = $this->_rankingRepo->getLatestBefore($date);
		if (!is_array($rankingData)) {
			$rankingData = array();
		}
		
		return $rankingData;
	}
	
	/**
	 * zapisuje graczom dane o aktualnym rankingu
	 * 
	 * @return void
	 */
	private function _saveRankingForPlayers()
	{
		//sprytnym zabiegiem wycigamy ranking na poczatek jutrzejszego dnia, czyli na dzisiaj :)
		$date = date("Y-m-d", time()+24*3600);
		$latestRankingData = $this->_getRankingDataBeforeDate($date);
		$players = $this->_playersRepo->getAll();
		foreach($players as $player) {
			$player->ranking = $latestRankingData[$player->id]->ranking;
			$this->_playersRepo->save($player);
		}
	}
}