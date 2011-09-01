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
	 * konstruktor
	 * jesli podano tablice - sluzy ona do wypelnienia obiektu danymi
	 * @param array $data [optional]
	 */
	public function __construct(array $data = array()) 
	{
		$this->fillFromArray($data, true);
	}
	
	/**
	 * wypelnia obiekt danymi z tablicy, klucze tablicy mapowane sa na pola obiektu
	 * 
	 * @param array $array
	 * @param boolean $initial [optional] czy jest to pierwsze wypelnienie obiektu (false w przypadku modyfikowania obiektu)
	 */
	public function fillFromArray($array, $initial = false)
	{
		$stringHelper = new FStringHelper();
		foreach ($array as $key => $val)
		{
			if ($val !== null )
			{
				$field = $stringHelper->toCamelCase($key);
				$this->$field = $val;
				if (!$initial) {
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
			if (in_array($field, array('_modifiedFields'))) {
				continue;
			}
			//wycinamy poczatkowy podkreslnik
			$field = (substr($field,0,1) == '_') ? substr($field,1) : $field;
			$field = $stringHelper->fromCamelCase($field);
			$arr[$field] = $v;
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
    		$this->_modifiedFields[$fieldName] = true;
    	}
    	elseif (property_exists($this, '_'.$fieldName)){
    		$fieldName = '_'.$fieldName;
    		$this->$fieldName = $val;
    		$this->_modifiedFields[$fieldName] = true;
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
    
    
    
}
?>