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
    	
    	return FController::OK;
    }
    
    
    public function showRanking()
    {
    	$users = $this->_usersRepo->getAll();
    	$playersRepo = new FRepository('SquashPlayerEntity');
    	$players = array();
    	foreach ($users as $user) {
    		$player = $playersRepo->getById($user->id);
    		$players[] = $player;
    	}
    	var_dump($players);
    	return FController::OK;
    }
    
    public function tryPlayer()
    {
    	$usersRepo = new FRepository('SquashPlayerEntity');
    	$karolArr = array (
    			'id'		=>	'22',
    			'name'		=>	'Karol3',
    			'short_name'=>	'KT3',
    			'email'		=>	'kt3@goldenline.pl',
    			'pass'		=>	md5('1234'),
    			'ranking'	=>	1200	
    	);
    	
    	$karol = new SquashPlayerEntity($karolArr);
    	$karol->name = 'Karol31';
    	$karol->ranking = 1201;
    	$usersRepo->save($karol);
    	
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
    	
    	//$results	= $this->_squashResultRepo->getResultsPlayedAt($date);
    	$results	= $this->_squashResultRepo->getAllBy(array('playedAt' => $date));
    	//indeksujemy tablice wynikow wg id graczy
    	$resultsByPlayers = array();
    	if (is_array($results)) {
	    	foreach ($results as $result) {
	    		$resultsByPlayers[$result->playerOneId][$result->playerTwoId] = $result;
	    	}
    	}
    	
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
}





?>