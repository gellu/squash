<?php
/**
* 
*/
class SquashController extends FController
{
	/**
	 * repozyorium wyynikow
	 * 
	 * @var SquashResultRepository
	 */
	private $_squashResultRepo;
	
	/**
	 * repozytorium userow
	 * 
	 * @var FUserRepository
	 */
	private $_usersRepo;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->_squashResultRepo = new SquashResultRepository();
		$this->_usersRepo = new FUserRepository();
	}
	
	
    public function index()
    {
    	$this->requireLogged();
        
    	$lastDate	= $this->_squashResultRepo->getLastPlayedDate();
		$this->_getDataForPlayedAtDate($lastDate);
		$this->assign('layoutTitle', "Wyniki Squash'a");
        return FController::OK;
    }
    
    /**
     * pokazuje wyniki dla podaje daty
     * 
     * @param string $date
     * @return string
     * @return
     */
    public function showDate($date)
    {
    	$this->requireLogged();
    	
    	$this->_getDataForPlayedAtDate($date);
    	$this->setPage('squash/index');
    	$this->assign('layoutTitle', "Wyniki Squash'a");
    	return FController::OK;
    }
    
    
    public function ranking()
    {
    	$this->requireLogged();
    	
    	$playersRepo = new FRepository('SquashPlayerEntity');
    	$players	 = $playersRepo->getAll();
    	$arrayHelper = new FArrayHelper();
    	$players	 = $arrayHelper->sortByField($players, 'ranking', SORT_DESC);
    	$this->assign("players", $players);
    	
    	$rankingRepo = new SquashRankingStateRepository();  
    	$maxRankingDate = $rankingRepo->getMaxDate();
		$this->assign("last_ranking", $rankingRepo->getLastRank());
		$this->assign("date", date("Y/m/d", strtotime($maxRankingDate)));
    	$this->assign('layoutTitle', "Wyniki Squash'a");
    	return FController::OK;
    }
    
    public function rankingStats($singlePlayerId = null)
    {
    	$this->requireLogged();
    	
    	$rankingRepo	= new SquashRankingStateRepository();
    	if (!empty($singlePlayerId)) {
    		$rankingStates = $rankingRepo->getAllBy(array('playerId' => $singlePlayerId));
    	} else {
    		$rankingStates = $rankingRepo->getAll();
    	}

    	$this->assign("plotData", $this->_getRankingPlotData($rankingStates));
    	
    	$playersRepo = new FRepository('SquashPlayerEntity');
    	/*if (!empty($singlePlayerId)) {
    		$player	= $playersRepo->getById($singlePlayerId);
    		$this->assign("players", array( $singlePlayerId => $player));
    		$this->assign("player", $player);
    	} else {*/
    		$players	 = $playersRepo->getAll();
    		$arrayHelper = new FArrayHelper();
	    	$players	 = $arrayHelper->indexByField($players, 'id');
	    	$this->assign("players", $players);
    	//}
    	
    	
    	
    	return FController::OK;	
    }
    
    public function buildRanking()
    {
    	$this->requireLogged();
    	
    	$rankingRepo = new SquashRankingStateRepository();
    	$resultsRepo = new SquashResultRepository();
    	$playersRepo = new FRepository('SquashPlayerEntity');
    	
    	$rankingRepo->truncate();
    	$rankingBuilder = new SquashRankingBuilder($rankingRepo, $resultsRepo, $playersRepo);
    	$rankingBuilder->computeAll();
    	
    	$this->redirect(ROOT_WWW.'/squash/ranking');
    }
    
    /**
     * pobiera wyniki dla podanej daty i ustawia do smrtow dane dot. wynikow i graczy
     * 
     * @param string $date
     * @return void
     */
    private function _getDataForPlayedAtDate($date)
    {
    	if (strtotime($date) === false) {
    		throw new Exception("wrong date format");
    	}
    	
    	list($prevDate, $nextDate) = $this->_squashResultRepo->getNeighbouringDates($date);
    	
    	//$results	= $this->_squashResultRepo->getResultsPlayedAt($date);
    	$results	= $this->_squashResultRepo->getAllBy(array('playedAt' => $date));
    	//indeksujemy tablice wynikow wg id graczy
    	$resultsByPlayers = array();
    	if (is_array($results)) {
	    	foreach ($results as $result) {
	    		$resultsByPlayers[$result->playerOneId][$result->playerTwoId] = $result;
	    	}
    	}
    	//var_dump($results);
    	$players = array();
    	$allPlayers = $this->_usersRepo->getAll();
    	foreach ($allPlayers as $player) {
    		$players[$player->id] = $player;
    	}
    	
    	$this->assign('date', $date);
    	$this->assign('prevDate', $prevDate);
    	$this->assign('nextDate', $nextDate);
    	$this->assign('results', $resultsByPlayers);
    	$this->assign('players', $players);	
    }
    
    /**
     * na podstawie danych o wartosciach rankingow dla graczy tworzy odpowiednio posortowane i zaindeksowane dane do wykresu
     * zwraca tablice postaci $plotData[$userId][$date] = ranking
     * 
     * @param array $rankingStates tablica obiektow RankingStateEntity
     * @return array
     */
    private function _getRankingPlotData($rankingStates)
    {
    	$arrayHelper	= new FArrayHelper();
    	$rankingStates	= $arrayHelper->indexByField($rankingStates, 'playerId', false);
    	
    	$plotData = array();
    	foreach ($rankingStates as $playerId => $playerRankingStates) {
    		$playerRankingStates = $arrayHelper->sortByField($playerRankingStates, 'validFor', SORT_ASC);
    		foreach ($playerRankingStates as $state) {
    			$plotData[$playerId][strtotime($state->validFor)] = (int)$state->ranking;
    		}
    	}
    	
    	return $plotData;
    }
}





?>