<?php
/**
 * wyjatki rzucane przez FRepository
 * 
 * @author karol
 *
 */
class FRepositoryException extends FBaseException {}

/**
 * sluzy do komunikacji modelu z baza danych
 * 
 * @author karol
 *
 */
class FRepository extends FBase{
	
	/**
	 * uchwyt to bazy danych
	 * 
	 * @var FDB
	 */
	protected $_db = null;
		
	
	public function __construct()
	{
		$this->_db = FLite::getInstance()->getDB();
	}
	
	
	/**
	 * obiekt do dodania do bazy danych
	 * 
	 * @param FEntity $entity
	 * @param boolean $replace czy insert ma byc zamieniony na replace
	 */
	public function save(FEntity $entity, $replace = false)
	{
		if ($this->_id) {
			return $this->update($entity);
		}
		
		$class = get_class($this);
		$table = str_ireplace('Model', '', $class);
		$table = $this->_fromCamelCase($table);
		$fields = array();

		foreach ($this as $f => $v)
		{
			if (in_array($f, array('_id', '_modifiedFields', ))) {
				continue;
			}
			
			$f = (substr($f,0,1) == '_') ? substr($f,1) : $f;
			if ($f == 'created_at' && is_null($v)) {
				$fields[$f] = 'NOW()';
			}
			if ($v === null) {
				continue;	
			}
			$fields[$f] = "'" . (is_array($v) ? $this->_db->escape(json_encode($v)) : $this->_db->escape($v)) . "'";
		}
		
		$type = $replace ? "REPLACE" : "INSERT";
		$q = sprintf("$type INTO $table (%s) VALUES (%s)", implode(',', array_keys($fields)), implode(',', array_values($fields)));
		$stat = $this->_db->query($q);
		if ($stat)
		{
			$this->_id = $this->_db->getLastInsertId();
			return true;
		}
		else
			return false;
		
		
	}
	
	public function delete()
	{
		$class = get_class($this);
		$table = strtolower(str_ireplace('Model', '', $class));
		$q = sprintf("DELETE FROM $table WHERE `id` = '%d'", $this->_id);
		return $this->_db->query($q);
		
	}
	
	/**
	 * aktualizuje dane a w bazie
	 * 
	 * @param FEntity $entity
	 * @return
	 */
	public function update(FEntity $entity)
	{
		
		$class = get_class($this);
		$table = strtolower(str_ireplace('Model', '', $class));
		$db = FLite::getInstance()->getDB();
		$fields = array();

		foreach ($this->_modifiedFields as $f => $tmp)
		{
			if ($f == '_id')
				continue;
				
			//$f = (substr($f,0,1) == '_') ? substr($f,1) : $f;
			$fields[] = sprintf("`%s` = '%s'", ((substr($f,0,1) == '_') ? substr($f,1) : $f) , (is_array($this->$f) ? $db->escape(json_encode($this->$f)) : $db->escape($this->$f)));
		}
		

		$q = sprintf("UPDATE $table SET %s WHERE id = '%d'", implode(',', $fields), $this->_id);
		
		return $db->query($q);
		
	}
	
	/**
	 * zwraca obiekt z bazy danych wg podanego id
	 * 
	 * @param integer $id
	 * @return FEntity
	 */
	public function getById($id)
	{
		$sql = "SELECT * FROM ".$this->_getTableName()." WHERE id = '".$id."'";
		$data = $this->_db->getRow($sql);
		if ($data === null) {
			return null;
		}
		
		$entityClass = $this->_getEntityClassName();
		return new $entityClass($data);
	}
	
	/**
	 * zwraca obiekt wg podanych warunkow
	 * 
	 * @param array $conditions array('field' => value )
	 * @return FEntity
	 */
	public function getOneBy(array $conditions)
	{
		$stringHelper	= new FStringHelper();
		$conditionsStr	= array();
		foreach ($conditions as $field => $value) {
			$conditionsStr[] = "`".$stringHelper->fromCamelCase($field)."` = '".$value."'";
		}
		
		$sql	= "SELECT * FROM ".$this->_getTableName()." WHERE ".implode(' AND ', $conditionsStr);
		$data	= $this->_db->getRow($sql);
		if ($data === null) {
			return null;
		}
		
		$entityClass = $this->_getEntityClassName();
		return new $entityClass($data);
	}
	
	/**
	 * zwraca nazwe tabeli dla uzywanej wlasnie klasy repozytorium
	 * 
	 * @return string
	 */
	protected function _getTableName()
	{
		$class = get_class($this);
		$class = str_ireplace('Repository', '', $class);
		//wywlamy F z poczatku nazwy klas frameworkowych
		if (substr($class, 0, 1) == 'F') {
			$class = substr($class, 1);
		}
		
		$stringHelper = new FStringHelper();
		$table = $stringHelper->fromCamelCase($class);
		$table = strtolower($table);
		return $table;
	}
	
	/**
	 * zwraca nazwe klase encji dla uzywaneho wlasnie repozytorium
	 * nazwa klasy wyznaczana jest przez podmiane Repository na Entity
	 */
	protected function _getEntityClassName()
	{
		$class = get_class($this);
		$class = str_ireplace('Repository', 'Entity', $class);
		
		return $class;
	}
    
}
?>