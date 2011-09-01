<?php
/**
* 
*/
class FController extends FBase
{
    const OK = "ok";
    const ERR = "err";
    
    protected $_smarty = null;
    
    protected $_params = array();
    protected $_page = null;
    
    
    function __construct()
    {
        $this->_smarty = FLite::getInstance()->getSmarty();
    }
    
    protected function assign($name, $value)
    {
        $this->_smarty->assign($name, $value);
    }
    
    protected function redirect($url)
    {
        header("Location: " . $url);
        FLite::getInstance()->getLogger()->log("redirecting to " . $url, LOG_INFO);
        exit;
    }
    
    protected function requireLogged()
    {
    	if (!FLite::getInstance()->getAuthManager()->getCurrentUser()) {
    		$this->redirect(ROOT_WWW);
    	}
    }
    
    protected function setLayout($lay)
    {
        FLite::getInstance()->setLayout($lay);
    }
    
    protected function setPage($page)
    {
        $this->_page = $page;
    }
    
    public function getPage()
    {
        return $this->_page;
    }
    
    public function setParams(array $params)
    {
    	$this->_params = $params;
    }
    
    
   
}





?>