<?php
/**
 * wyjatki rzucane przez FEntity i klasy z niej dziedziczace
 * @author karol
 */
class FEntityException extends FBaseException {}

/**
 * reprezentuje podstawe klasy do obslugi encji
 * 
 * @author Karol...
 */
class FEntity extends FBase{
	
	/**
	 * stan nowy, obiekt zostal wlasnie utworzony, wszystkie pola sa puste
	 * 
	 * @var int
	 * @see $_state
	 */
	const STATE_NEW = 1;
	
	/**
	 * obiekt w czasie inicjalizacji - jest obecnie wypelniany danymi podanymi do konstruktora (np dane z bazy)
	 * 
	 * @var int
	 * @see $_state
	 */
	const STATE_INITIALIZATION = 2;
	
	/**
	 * obiekt zostal wypelniony danymi z kostruktora
	 * 
	 * @var int
	 * @see $_state
	 */
	const STATE_FILLED = 3;
	
	/**
	 * obiekt zostal zmodyfikowany poza kostruktorem
	 * 
	 * @var int
	 * @see $_state
	 */
	const STATE_TOUCHED = 4;
	
	
	/**
	 * identyfikator encji
	 * 
	 * @var integer
	 */
	protected $_id;
	
	/**
	 * tablica, ktorej kluczami sa zmodyfikowane pola encji
	 * 
	 * @var array
	 */
	protected $_modifiedFields = array();
	
	/**
	 * stan obiektu - np pusty, w czasie wypelniania, wypelniony
	 * 
	 * @var int
	 */
	private $_state;
	
	/**
	 * konstruktor
	 * jesli podano tablice - sluzy ona do wypelnienia obiektu danymi
	 * @param array $data [optional]
	 */
	public function __construct(array $data = array()) 
	{
		$this->_state = self::STATE_NEW;
		
		if (!empty($data)) {
			$this->_state = self::STATE_INITIALIZATION;
			$this->fillFromArray($data);
			$this->_state = self::STATE_FILLED;
		}
	}
	
	/**
	 * wypelnia obiekt danymi z tablicy, klucze tablicy mapowane sa na pola obiektu
	 * 
	 * @param array $array
	 * @param boolean $initial [optional] czy jest to pierwsze wypelnienie obiektu (false w przypadku modyfikowania obiektu)
	 */
	public function fillFromArray(array $array)
	{
		$stringHelper = new FStringHelper();
		foreach ($array as $key => $val)
		{
			if ($val !== null )
			{
				$field = $stringHelper->toCamelCase($key);
				$this->$field = $val;
				if ($this->_state != self::STATE_INITIALIZATION) {
					$this->_modifiedFields[$field] = true;	
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * zwraca tablice z zawartoscia obiektu, pola mapowane sa na indeksy tablicy
	 * 
	 * @author Karol
	 * @return array
	 */
	public function toArray()
	{
		$stringHelper = new FStringHelper();
		
		$arr = array();
		foreach($this as $field => $value)
		{
			if (in_array($field, array('_modifiedFields', '_state'))) {
				continue;
			}
			//wycinamy poczatkowy podkreslnik
			$field = (substr($field,0,1) == '_') ? substr($field,1) : $field;
			$field = $stringHelper->fromCamelCase($field);
			$arr[$field] = $value;
		}
		
		return $arr;
	}
	
	/**
	 * magic set
	 * 
	 * @param string $fieldName
	 * @param mixed $val
	 * @return boolean
	 */
	public function __set($fieldName, $val)
    {
		if (method_exists($this, 'set'.ucfirst($fieldName))) {
			return call_user_method('set'.ucfirst($fieldName), $this, $val);
		}
		if (property_exists($this, $fieldName)){
    		$this->$fieldName = $val;
    		if ($this->_state != self::STATE_INITIALIZATION) {
    			$this->_modifiedFields[$fieldName] = true;
    			$this->_state = self::STATE_TOUCHED;
    		}
    	}
    	elseif (property_exists($this, '_'.$fieldName)){
    		$fieldName = '_'.$fieldName;
    		$this->$fieldName = $val;
    		if ($this->_state != self::STATE_INITIALIZATION) {
    			$this->_modifiedFields[$fieldName] = true;
    			$this->_state = self::STATE_TOUCHED;
    		}
    	} else {
    		throw new FEntityException("Trying to set unknown field " . $fieldName);
    	}
    	
    	return true;
    	
    }
    
    /**
     * magic get
     * 
     * @param string $field
     * @return mixed
     */
    public function __get($field)
    {
    	if (method_exists($this, 'get'.ucfirst($field)))
		{
			return call_user_method('get'.ucfirst($field), $this);
		}
    	
    	
    	//$field = $this->_fromCamelCase($field);
    	if (property_exists($this, $field)){
    		return $this->$field;
    	}
    	elseif (property_exists($this, '_'.$field)){
    		$field = '_'.$field;
    		return $this->$field;
    	} else {
    		return false;
    	}
    } 
    
    /**
     * zwraca nazwy pol encji (z pominieciem _modifiedFields i _state)
     * 
     * @param boolean $withId czy zwrocic rowniez pole _id
     * @return array
     */
    public function getEntityFieldNames($withId = true)
    {
    	$variablesList = get_object_vars($this);
    	return $this->_getClearFieldNames($variablesList, $withId);
    }
    
    /**
     * zwraca nazwy pol rodzica encji (z pominieciem _modifiedFields i _state)
     * 
     * @param boolean $withId
     * @return array
     */
    public function getEntityParentFieldNames($withId = false)
    {
    	$variablesList = get_class_vars(get_parent_class($this));
    	return $this->_getClearFieldNames($variablesList, $withId);
    }
    
    /**
     * na podstawie listy pol/zmiennych, zwraca liste pol encji 
     * 
     * @param array $variablesList
     * @param boolean $withId
     * @return array
     */
    private function _getClearFieldNames($variablesList, $withId)
    {
    	$fieldNames = array_keys($variablesList);
    	
    	/*
    	$dbFields = array();
    	$stringHelper	= new FStringHelper();
    	foreach ($variablesList as $name => $value) {
    		if ($name == '_modifiedFields' || $name == '_state') {
    			continue;
    		}
    		
    		if ($name == '_id' && !$withId) {
    			continue;
    		}
    		
    		$field	 = (substr($name,0,1) == '_') ? substr($name,1) : $name;
    		$dbField = $stringHelper->fromCamelCase($field);
    		$dbFields[] = $dbField;
    	}
    	*/
    	
    	$allFieldNamesT = array_flip($fieldNames);
    	 
    	//wywala niepotrzebne pola
    	unset($allFieldNamesT['_modifiedFields'], $allFieldNamesT['_state']);
    	if (!$withId) {
    		unset($allFieldNamesT['_id']);
    	}
    	 
    	return array_flip($allFieldNamesT);
    }
    
    /**
     * zwraca nazwy zmodyfikowanych pol 
     * 
     * @return array
     */
    public function getModifiedFieldNames()
    {
    	return array_keys($this->_modifiedFields);
    }
    
    /**
    * tworzy encje rodzica dla podanej encji (rozszerzajacej encje inna niz podstawowa FEntity)
    *
    * @throws FEntityException jesli podana encja rozszerza tylko FEntity
    * @return FEntity
    */
    public function createParentEntity()
    {
    	$parentClass	= get_parent_class($this);
    	if ($parentClass == 'FEntity') {
    		throw new FEntityException("you cannot create parent Entity for entity extending only FEntity");
    	}
    
    	$entityData		= $this->toArray();
    	$arrayHelper	= new FArrayHelper();
    	$stringHelper	= new FStringHelper();
    	//pobieramy pola rodzica,
    	//zeby z danych dziecka wybrac tylko te, ktore sa potrzebne do stworzenia rodzica
    	$parentFieldNames	= $this->getEntityParentFieldNames();
    	$parentFieldsAsKeys	= array_flip($parentFieldNames);
    	$parentFieldsAsKeys = $arrayHelper->keysFromCamelCase($parentFieldsAsKeys);
    	//z danych dziecka zostawiamy tylko te, ktore sa rowniez danymi rodzica
    	$parentData	= array_intersect_key($entityData, $parentFieldsAsKeys);
    	//tworzymy instancje repo rodzica i zapisujemy do db
    	$parentEntity	= new $parentClass($parentData);
    
    	return $parentEntity;
    }
    
    
    
}
?>