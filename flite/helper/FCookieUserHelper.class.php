<?php
/**
 * @author Karol
 */
class FCookieUserHelper {

    public function getCurrentUniqueId()
    {
//    	if ($this->checkUniqueId())
//    	{
//    		$this->setUnique();
//    	}
//    	
		if (isset($_COOKIE[UNIQUE_COOKIE_NAME])) {
			return $_COOKIE[UNIQUE_COOKIE_NAME];	
		} else {
			return null;
		}
    	
    }
    
    public function setUniqueId()
    {
    	setcookie(UNIQUE_COOKIE_NAME, uniqid(), time()+3600*24*365*10, '/');
    }
    
    public function checkUniqueId()
    {
  		return isset($_COOKIE[UNIQUE_COOKIE_NAME]);
    }
}
?>