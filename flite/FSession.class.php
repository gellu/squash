<?php
/**
* 
*/
class FSession
{
    private $_id = null;
    private $_dbh = null;
    private $_data = null;
    private $_lastAccessTs = null;
    
    public function __construct($dbh)
    {
    	if (!$dbh)
    		throw new Exception("DBHandler is null");
    		
    	$this->setDBHandler($dbh);
    }
    
    public function start()
    {
    	if ($this->sessionStarted())
    		throw new Exception("session already started");
    	
    	$oldCookie = null;
    	if (isset($_COOKIE[SESSION_COOKIE_NAME])) {
    		$oldCookie = $_COOKIE[SESSION_COOKIE_NAME];
    	}
    	
    	if (!$oldCookie || !$this->_fillSessionFromDB($oldCookie))
    	{
    		$this->_id = uniqid(time(), true);
    		setcookie(SESSION_COOKIE_NAME, $this->_id, time()+SESSION_EXPIRES, '/');
    		$this->_data = array();
    	}
    	
    	$this->_touchSession();
    	
    }
    
    public function set($name, $value)
    {
    	if (!$this->_id)
    		throw new Exception("Session not started");
    	
    	$oldVal = isset($this->_data[$name]) ? $this->_data[$name] : null;
    	
    	$this->_data[$name] = $value;
    	if ($this->_save2DB())
    	{
    		$this->_touchSession();
    		return true;
    	}
    	else 
    	{
    		if ($oldVal !== null)
    			$this->_data[$name] = $oldVal;
    		return false;
    	}
    }
    
    public function get($name)
    {
    	if (!$this->_id) {
    		throw new Exception("Session not started");
    	}
    	
    	if ($this->_lastAccessTs + SESSION_EXPIRES < time()) {
    		$this->destroy();
    	}
    
    	$this->_touchSession();
    	
    	return isset($this->_data[$name]) ? $this->_data[$name] : null;
    	
    }
    
    public function getAll()
    {
    	return $this->_data;
    }
    
    public function remove($name)
    {
    	//unset($this->_data[$name]);
    	$oldVal = isset($this->_data[$name]) ? $this->_data[$name] : null;
    	
    	unset($this->_data[$name]);
    	if ($this->_save2DB())
    	{
    		$this->_touchSession();
    		return true;
    	}
    	else 
    	{
    		if ($oldVal !== null)
    			$this->_data[$name] = $oldVal;
    		return false;
    	}
    }
    
    public function destroy()
    {
    	if (!$this->_id)
    		throw new Exception("Session not started");
    		
    	if ($this->_dbh->query(sprintf("DELETE FROM session WHERE id = '%s'", $this->_id)))
    	{
    		$this->_id = null;
    		$this->_data = null;
    		$this->_lastAccessTs = 0;
    		return true;
    	}
    	else
    		return false;
    }
    
    private function _fillSessionFromDB($id)
    {
    	//TODO: wyciaganie z bazy tylko sesji ktorej nie uplynal expires
    	//$sess = $this->_dbh->getRow("SELECT * FROM session WHERE id = '$id' AND updated_at < DATE_INTERVAL (NOW() + ".SESSION_EXPIRES.")");
    	
    	$sess = $this->_dbh->getRow("SELECT * FROM session WHERE id = '$id'");
    	if ($sess) 
    	{
    		$this->_id = $id;
    		$this->_data = $this->_decodeData($sess['data']);
    		
    		return $sess;
    	}
    	else
    		return null; 
    		
    }
    
    private function _save2DB()
    {
    	return $this->_dbh->query(sprintf("REPLACE INTO session (id, data, updated_at) VALUES ('%s', '%s', NOW())", $this->_id, $this->_getDataEncoded()));
    }
    
    
    private function _getDataEncoded()
    {
    	return json_encode($this->_data);
    }
    
    private function _decodeData($data)
    {
    	return json_decode($data, true);
    }
    
    private function _touchSession()
    {
    	$this->_lastAccessTs = time();
    }
    
    public function setDBHandler($dbh)
    {
    	$this->_dbh = $dbh;
    }
    
    
    public function sessionStarted()
    {
    	return ($this->_id) ? true : false;
    }
    
}

?>