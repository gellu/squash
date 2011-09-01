<?php
/**
* 
*/
class FDB extends FBase
{
    protected $_conn = null;
    private $_queriesCounter = 0;
    private $_rowsAffected = null;
    private $_lastInsertId = null;
    private $_lastResult = null;
    private $_exec_time = 0;
    
    public function __construct()
    {
        $this->_conn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$this->_conn)
            trigger_error('cannot connect to db host: '.mysql_error(), E_USER_ERROR);
        if (!mysql_select_db(DB_NAME, $this->_conn))
            trigger_error('cannot select databse '.DB_NAME.': '.mysql_error(), E_USER_ERROR);
            
    }    
    
    public function query($sql)
    {
        $this->_queriesCounter++;
        $sql = trim($sql);
        list($qryType,) = explode(" ", strtolower($sql));
        
        $this->_lastResult = null;
        $this->_lastInsertId = null;
        $this->_rowsAffected = null;
        
        $start = microtime(true);
        $q = mysql_query($sql);
        $executionTime = (microtime(true) - $start);
        $this->_exec_time += $executionTime; 
        
        $this->_logQuery($sql, $executionTime);
        
        if (!$q) {
            return null;
        }
        if ($qryType == "select")
        {
            $res = array();
            while ($row = mysql_fetch_assoc($q))
                $res[] = $row;
            $this->_lastResult = $res;
            return $this->_lastResult;
        }
        else
        {
            $this->_rowsAffected = mysql_affected_rows($this->_conn);
            if ($qryType == "insert" || $qryType == "replace")
                $this->_lastInsertId = mysql_insert_id($this->_conn);
                
            return true;
        }
    }
    
    public function getVar($sql)
    {
        $this->query($sql);
        if ($this->_lastResult)    
        {
            list($ret,) = array_values($this->_lastResult[0]);
            return $ret;
        }
        else
            return null;
    }
    
    public function getRow($sql)
    {
        $this->query($sql);
        if ($this->_lastResult) {
            return $this->_lastResult[0];
        } else {
            return null;
        }
    }
    
    public function getCol($sql)
    {
        $this->query($sql);
        if ($this->_lastResult)
        {
        	$col = array();
        	foreach ($this->_lastResult as $row)
        	{
        		list($col[],) = array_values($row);
        	}
        	
        	return $col;
        }        
        else
            return null;
    }
    
    public function getResults($sql)
    {
        $this->query($sql);
        if ($this->_lastResult)    
            return $this->_lastResult;
        else
            return null;
    }
    
    public function begin($level = 'SERIALIZABLE')
    {
    	mysql_query ('SET TRANSACTION ISOLATION LEVEL '.$level, $this->_conn);
		return mysql_query('START TRANSACTION', $this->_conn) ? true : false;
    }
    
    public function commit()
	{
	    return mysql_query('COMMIT', $this->_conn) ? true : false;
	}
	
	public function rollback()
	{
	    return mysql_query('ROLLBACK', $this->_conn) ? true : false;
	} 
    
    public function getOverallExecutionTime()
    {
    	return $this->_exec_time;
    }
    
    public function escape($data)
    {
    	return mysql_real_escape_string($data);
    }
    
    public function getLastInsertId()
    {
    	return $this->_lastInsertId;
    }
    
    public function update()
    {
    	//TODO
    	throw new Exception("Not yet implemented");
    }
    
    public function delete()
    {
    	//TODO
    	throw new Exception("Not yet implemented");
    }
    
    public function insert()
    {
    	//TODO
    	throw new Exception("Not yet implemented");
    }
    
    public function replace()
    {
    	//TODO
    	throw new Exception("Not yet implemented");
    }
    
    /**
     * zapisuje sqlke z jej czasem wykonania do pliku z logiem i do debuga
     * 
     * @param string $query
     * @param float $time czas w sekundach
     * @return void
     */
    private function _logQuery($query, $time)
    {
    	$displayExecutionTime = round($time*1000,2);
    	$timeTag = '['.$displayExecutionTime.'ms] ';
    	
    	FLite::getInstance()->getLogger()->log($timeTag . $query, LOG_DEBUG);
    	FLite::getInstance()->getDebugger()->dumpSql($query, $displayExecutionTime);
    }
    
        
}

?>