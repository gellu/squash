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
        
    	//$user = FLite::getInstance()->getAuthManager()->getCurrentUser();

		$lastDate	= $this->_squashResultRepo->getLastPlayedDate();
		$this->_getDataForPlayedAtDate($lastDate);
		
		
        return FController::OK;
    }
    
    public function showDate($date)
    {
    	$this->requireLogged();
    	
    	$this->dump($date);
    	$this->_getDataForPlayedAtDate($date);
    	$this->setPage('squash/index');
    	
    	return FController::OK;
    }
    
    /**
     * pobiera wyniki dla podanej daty i ustawia do smrtow dane dot. wynikow i graczy
     * 
     * @param string $date
     * @return void
     */
    private function _getDataForPlayedAtDate($date)
    {
    	list($prevDate, $nextDate) = $this->_squashResultRepo->getNeighbouringDates($date);
    	
    	$results	= $this->_squashResultRepo->getResultsPlayedAt($date);
    	//indeksujemy tablice wynikow wg id graczy
    	$resultsByPlayers = array();
    	foreach ($results as $result) {
    		$resultsByPlayers[$result->playerOneId][$result->playerTwoId] = $result;
    	}
    	
    	//zbieramy dane o graczach
    	$playerIds = array_keys($resultsByPlayers);
    	$players = array();
    	//TODO: wybieranie jednym zapytaniem
    	foreach ($playerIds as $playerId) {
    		$players[$playerId] = $this->_usersRepo->getById($playerId);
    	}
    	
    	$this->assign('date', $date);
    	$this->assign('prevDate', $prevDate);
    	$this->assign('nextDate', $nextDate);
    	$this->assign('results', $resultsByPlayers);
    	$this->assign('players', $players);	
    }
}





?>