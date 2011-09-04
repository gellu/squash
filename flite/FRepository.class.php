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
	 * @param	FEntity $entity encja/obiekt do wstawienia do bazy
	 * @param	boolean $replace czy insert ma byc zamieniony na replace
	 * @return	boolean true on success
	 * @throws	FRepositoryException jesli nie udalo sie zapisac
	 */
	public function save(FEntity $entity, $replace = false)
	{
		if ($entity->id) {
			return $this->update($entity);
		}
		
		$table = $this->_getTableName();
		$entityFields	= $entity->getEntityFieldNames($withId = false);
		$fieldsToSave	= array();
		$stringHelper	= new FStringHelper();
		
		foreach ($entityFields as $field)
		{
			$field	 = (substr($field,0,1) == '_') ? substr($field,1) : $field;
			$dbField = $stringHelper->fromCamelCase($field);
			if ($field == 'createdAt' && is_null($entity->$field)) {
				//z automatu ustawiamy createdAt
				$fieldsToSave[$dbField] = 'NOW()';
			} else {
				//zamieniamy array na jsona, escapujemy i pakujemy w ciapki
				$value = is_array($entity->$field) ? json_encode($entity->$field) : $entity->$field;
				$value = $this->_db->escape($value);
				$fieldsToSave[$dbField] = "'" . $value . "'";
			}
		}
		
		$type	= $replace ? "REPLACE" : "INSERT";
		$q		= sprintf("$type INTO $table (%s) VALUES (%s)", implode(',', array_keys($fieldsToSave)), implode(',', array_values($fieldsToSave)));
		
		if ($this->_db->query($q))
		{
			$entity->id = $this->_db->getLastInsertId();
			return true;
		} else {
			throw new FRepositoryException("saving to ".$table." failed: " . $q);
		}
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
	 * @return boolean
	 * @throws FRepositoryException jesli zadne dane nie zostaly zmodyfikowane lub nie udalo sie zapisac
	 */
	public function update(FEntity $entity)
	{
		$table = $this->_getTableName();
		$stringHelper	= new FStringHelper();
		
		$fields = array();
		$modifiedFields = $entity->getModifiedFieldNames();
		if (empty($modifiedFields)) {
			throw new FRepositoryException("no field was modified");
		}

		foreach ($modifiedFields as $field => $tmp)
		{
			if ($field == '_id') {
				continue;
			}
			//wywalamy podkreslnik dla pol prywatnych encji
			$field	 = (substr($field,0,1) == '_') ? substr($field,1) : $field;
			//dostajemy pola w camelcase'ie a w bazie mamy z podkreslnikami
			$dbField = $stringHelper->fromCamelCase($field);
			
			$value = is_array($entity->$field) ? json_encode($entity->$field) : $entity->$field;
			$value = $this->_db->escape($value);
			$fieldsToSaveStr[] = "`". $dbField."` = '" . $value . "'";
		}
		

		$q = sprintf("UPDATE $table SET %s WHERE id = '%d'", implode(',', $fieldsToSaveStr), $entity->id);

		if ($this->_db->query($q)) {
			return true;
		} else {
			throw new FRepositoryException("update'ing table ".$table." field");
		}
		
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
	 * @return FEntity|null
	 * @throws FRepositoryException przy pustej tablicy $conditions 
	 */
	public function getOneBy(array $conditions)
	{
		$stringHelper	= new FStringHelper();
		
		if (empty($conditions)) {
			throw new FRepositoryException("conditions array cannot be empty");
		}
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
