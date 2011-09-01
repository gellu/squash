<?php
/**
* 
*/
class FLite
{
    const MODE_WWW = 1;
    const MODE_AJAX_JSON = 2;
    const MODE_BATCH = 3;
    
    private static $_instance = null;
    private $_logger = null;
    private $_mode = null;
    private $_controller = null;
    private $_smarty = null;
    private $_db = null;
    private $_session = null;
    private $_debugger = null;
    private $_authManager = null;
    private $_layout = 'www';
    
    /**
     * zwraca instancje frameworku
     * 
     * @return FLite
     */
    public static function getInstance()
    {
    	if (self::$_instance == null)
            self::$_instance = new Flite();
       	
       	spl_autoload_register(array(self::$_instance, 'load'));
        
        return self::$_instance;
    }
    
//    public function __construct()
//    {
//    }
    
    /**
     * autoloader
     * klasy rozpoczynajace sie od litery F uznawane sa za czes frameworku
     * controllery,biblioteki,helpery i modele ladowane sa z odpowiednich dla siebie katalogow w aplikacji
     * 
     * @param string $className
     */
    public function load($className)
    {
    	//jesli zaczyna sie na f - zaciagnij z Flite
    	if (substr($className, 0, 1) == 'F')
    	{
    		if (stristr($className, 'entity') && $className != 'FEntity') {
    			include (ROOT_DIR.'/flite/entity/'.$className.'.class.php');
    		} elseif (stristr($className, 'repository') && $className != 'FRepository') {
    			include (ROOT_DIR.'/flite/repository/'.$className.'.class.php');	
    		} elseif (stristr($className, 'helper')) {
    			include (ROOT_DIR.'/flite/helper/'.$className.'.class.php');
    		} else {
    			include (ROOT_DIR.'/flite/'.$className.'.class.php');
    		}
    	} elseif (stristr($className, 'controller')) {
            include (ROOT_DIR.'/app/controller/'.$className.'.class.php');
    	} elseif (stristr($className, 'lib')) {
            include (ROOT_DIR.'/app/lib/'.$className.'.class.php');
    	} elseif (stristr($className, 'entity')) {
            include (ROOT_DIR.'/app/entity/'.$className.'.class.php');
		} elseif (stristr($className, 'repository')) {
			include (ROOT_DIR.'/app/repository/'.$className.'.class.php');
        } elseif (stristr($className, 'helper')) {
        	include (ROOT_DIR.'/app/helper/'.$className.'.class.php');
        }    
        
    }
    
    /**
     * ustawia tryb dzialania frameworku, dostpne wartosci to Flite::MODE_WWW, FLite::MODE_BATCH, FLite::MODE_AJAX_JSON
     * 
     * 
     * @param int $mode
     */
    public function setMode($mode)
    {
		$this->_mode = $mode;
        
		switch ($this->_mode)
		{
			case self::MODE_AJAX_JSON:
									$this->setLayout('json');
			case self::MODE_WWW:
			 	
									$this->initDB();
						   			//$this->initSmarty();
						   			
						   			break;
						   
			case self::MODE_BATCH: 	$this->initDB();
									break;
						
		} 
    }
    
    /**
     * uruchamia framework:
     * 	- ustawia naglowki tak aby uniknac cache'owania na proxy
     *  - odpala wyznaczenie kontrolera i metody
     *  - przypina zdefiniowane obiekty modelu,helperów, biblioteki
     *  - ustawia zmienne smartowe ROOT_WWW, DEBUG i current
     *  - zapisuje ciacho userowi (w przypadku MODE_WWW)
     *  - odpala kontroler i metode
     *  - wyswietla strone
     */
    public function go()
    {
    	header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
        header ('Cache-Control: no-cache');
        header ('Pragma: no-cache');
        header ('Expires: ' . gmdate('D, d M Y H:i:s', time()-86400) . ' GMT');
        header ('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()-86400) . ' GMT');
    	
        list($controllerName, $methodName, $params) = $this->_route();
        
        $c = new $controllerName();
        $c->setParams($params);
        
        $this->_smarty->assign('ROOT_WWW', ROOT_WWW);
        $this->_smarty->assign('DEBUG', DEBUG);
        $this->_smarty->assign('REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

        $this->_smarty->assign('currentUser', $this->getAuthManager()->getCurrentUser());
        
        $cookieUserHelper = new FCookieUserHelper();
        if ($this->_mode == self::MODE_WWW)
        {
        	if (!$cookieUserHelper->getCurrentUniqueId()) {
				$cookieUserHelper->setUniqueId();
			}
			 
			if (!is_callable(array($c, strtolower($methodName))))
        	{
        		$this->_smarty->assign('content', $this->_smarty->fetch('error/404.tpl'));
	            $this->_smarty->display('_layouts/'.$this->_layout.'.layout.tpl');
	            return;
        	}	
        	//var_dump($c, $methodName, $params);
	        if (call_user_func_array(array($c, strtolower($methodName)), $params) == FController::OK)
	        {
	        		if (DEBUG)
	        		{
	        			$this->getDebugger()->dumpVar($this->getSession()->getAll(), 'SESSION');
	        			$this->getDebugger()->dumpGlobals();
	        			$this->_smarty->assign('DEBUG_HTML', $this->getDebugger()->getHTML());
	        		}
	        		 
	        		if ($page = $c->getPage()) {
	        			$this->_smarty->assign('content', $this->_smarty->fetch($page . '.tpl'));
	        		} else {
	                	$this->_smarty->assign('content', $this->_smarty->fetch(strtolower(substr($controllerName, 0, -10)) .'/'. $methodName. '.tpl'));
	        		}
	        		
	                $this->_smarty->display('_layouts/'.$this->_layout.'.layout.tpl');
	        } else {
	        	throw new FBaseException("Controller must return FController::OK");
	        }
        }
        elseif ($this->_mode == self::MODE_AJAX_JSON)
        {
        	$ret = call_user_func_array(array($c, strtolower($methodName)), $params);
        	$this->_smarty->assign('stat', $ret);
        	
        	if ($page = $c->getPage())
	        	$this->_smarty->assign('resp', $this->_smarty->fetch($page . '.tpl'));
	        else
	            $this->_smarty->assign('resp', $this->_smarty->fetch(strtolower(substr($controllerName, 0, -10)) .'/'. $methodName. '.tpl'));
	            
	        $this->_smarty->display('_layouts/'.$this->_layout.'.layout.tpl');
	        
        }
        
        
    }
    
    
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (DEBUG)
        {
        
            echo '<div style="display:block; background-color:#eee"><br/>ha! error frajerze!!<br/>';
            switch ($errno) {
                case E_USER_ERROR:
                    echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                    echo "  Fatal error on line $errline in file $errfile";
                    echo "<br/>Aborting...<br /></div>\n";
                    exit(1);
                    break;
            
                case E_USER_WARNING:
                    echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
                    break;
            
                case E_USER_NOTICE:
                    echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
                    break;
            
                default:
                    echo "<small>Unknown error type: [$errno] $errstr<br />on line $errline in file $errfile</small>\n";
                    break;
            }
            echo '</div>';
        }    
    }
    
    
    /**
     * inicjalizuje handler bazy danych i wysyla kwerendy ustawiajace kodowanie zapytan na utf-8
     * 
     */
    public function initDB()
    {
        $this->_db = new FDB();
        $this->_db->query("SET CHARACTER SET utf8");
        $this->_db->query("SET NAMES utf8");
    }
    
    /**
     * zwraca handler do bazy danych
     * 
     * @return FDB
     */
    public function getDB()
    {
        return $this->_db;
    }
    
    /**
     * inicjalizuje obiekt smarty
     * 
     */
    private function _initSmarty()
    {
        include (ROOT_DIR.'/flite/lib/smarty/Smarty.class.php');
        $this->_smarty = new Smarty;

        //$smarty->use_sub_dirs = SMARTY_USE_SUB_DIRS;
        $this->_smarty->template_dir = ROOT_DIR . '/app/views';
        $this->_smarty->config_dir = ROOT_DIR . '/app/config';
        $this->_smarty->cache_dir = ROOT_DIR . '/app/var/cache';
        $this->_smarty->compile_dir = ROOT_DIR . '/app/var/templates_c';
    }
    
    /**
     * zwraca obiekt smarty (jesli nie istnieje, to go tworzy)
     * 
     * @return Smarty
     */
    public function getSmarty()
    {
    	if (!$this->_smarty)
    		$this->_initSmarty();

        return $this->_smarty;
    }
   	
   	/**
     * inicjalizuje obiekt sesji, wymaga zainicjowanych wczesniej bazy danych
     * 
     * @throws Exception w przypadku braku zainicjowanej bazdy danych
     */
   	private function _initSession()
   	{
   		if (!$this->getDB())
			throw new Exception("DB not initialized");
    	$this->_session = new FSession($this->_db);
   	}
   	
   	/**
     * zwraca obiekt sesji (jesli nie istnieje, to go tworzy)
     * 
     * @return FSession
     */
    public function getSession()
    {
    	if (!$this->_session)
    		$this->_initSession();
    	
    	if (!$this->_session->sessionStarted())
    		$this->_session->start();
        
        return $this->_session;
    }
    
    /**
     * inicjalizuje obiekt managera autoryzacji, wymaga zainicjowanych wczesniej bazy danych i sessji
     * 
     * @throws Exception w przypadku braku zainicjowanej bazdy danych lub sessji
     */
    private function _initAuthManager()
    {
		if (!$this->getDB())
			throw new Exception("DB not initialized");
		if (!$this->getSession())
			throw new Exception("Session not initialized");	
	
		$this->_authManager = new FAuthManager($this->getDB(), $this->getSession());	
    	
    }
    
    /**
     * zwraca obiekt menagera autoryzacji (jesli nie istnieje, to go tworzy)
     * 
     * @return FAuthManager
     */
    public function getAuthManager ()
    {
    	if (!$this->_authManager)
    		$this->_initAuthManager();
    			
    	return $this->_authManager;
    }
    
    /**
     * zwraca obiekt menagera autoryzacji (jesli nie istnieje, to go tworzy)
     * 
     * @return FDebug
     */
    public function getDebugger()
    {
    	if (!$this->_debugger)
    		$this->_debugger = new FDebug();
    	
    	return $this->_debugger;
    }
    
    public function setLayout($lay)
    {
        $this->_layout = $lay;
    }
    
    /**
     * ustala jaki kontroler i i jaka jego metoda ma byc wywolana dla podanego urla
     * rozbija request_uri (bez protokolu i domeny) po slashu - pierwsze pole to kontroler, drugie to metoda, kolejne to parametry przekazywane do metody
     * w przypadku braku nazwy metody wywolywana jest metoda okreslona stałą METHOD_MAIN
     * w przypadku braku nazwy kontrolera wywolywany jest kontroler okreslony stałą CONTROLLER_MAIN
     * w przypadku kilkuwyrazowej nazwy metody w urlu moze byc podana camelCasem lub z myslnikiem, 
     * przyklady w postaci url => kontroler::metoda
     * / 				=> CONTOLLER_MAIN::METHOD_MAIN
     * /photo			=> Photo::METHOD_MAIN
     * /photo/vote		=> Photo::vote
     * /photo/userVotes	=> Photo::userVotes
     * /photo/user-votes=> Photo::userVotes
     * /photo/show/5	=> Photo::vote i liczba 5 przekazana jako pierwszy parametr do metody 
     * 
     */
    private function _route()
    {
        $reqUri = substr($_SERVER['REQUEST_URI'], strlen(ROOT_URI));
        $reqArr = explode("?", $reqUri);
        if (isset($reqArr[1])) {
        	$getParams = $reqArr[1];
        }
        	
        $reqArr = explode("/", $reqArr[0]);
        if ($this->_mode == self::MODE_AJAX_JSON) {
        	array_shift($reqArr);
        }
        
        if (!empty($reqArr[1]))
        {
        	$func 		= create_function('$c', 'return strtoupper($c[1]);');
        	$controllerName = ucfirst($reqArr[1]);
        	$controllerName = preg_replace_callback('/-([a-z])/i', $func, $controllerName);
            $controllerName = ucfirst($controllerName);
            
            if (!empty($reqArr[2]))
            {	
                $methodName = $reqArr[2];
                $methodName = preg_replace_callback('/[-_]([a-z])/i', $func, $methodName);
               	$params 	= array_slice($reqArr, 3);
            }
            else
            {
                $methodName = METHOD_MAIN;
                $params 	= array();
            }
                
            
        }
        else
        {
            $controllerName = CONTROLLER_MAIN;
            $methodName 	= METHOD_MAIN;
            $params 		= array();
        }
        
        return array($controllerName.'Controller', $methodName, $params);
    }
    
    /**
     * zwraca obiekt Loggera
     * 
     * @return FLog
     */
    public function getLogger ()
	{
		if (!$this->_logger instanceof FLog) {
			$this->_logger = new FLog;
		}
		
		switch ($this->_mode)
		{
			case self::MODE_WWW:	$this->_logger->setLogLevel(LOG_LEVEL_WWW);
									$this->_logger->setLogOutput(LOG_OUTPUT_WWW);
									$this->_logger->setLogFile(LOG_FILE_WWW, LOG_AGGREGATE_WWW);
									break;
			case self::MODE_BATCH:	$this->_logger->setLogLevel(LOG_LEVEL_WWW);
									$this->_logger->setLogOutput(LOG_OUTPUT_WWW);
									$this->_logger->setLogFile(LOG_FILE_WWW, LOG_AGGREGATE_WWW);
									break;
			case self::MODE_AJAX_JSON:	$this->_logger->setLogLevel(LOG_LEVEL_WWW);
									$this->_logger->setLogOutput(LOG_OUTPUT_WWW);
									$this->_logger->setLogFile(LOG_FILE_WWW, LOG_AGGREGATE_WWW);
									break;
		}					

		return $this->_logger;
	}
    
}





?>