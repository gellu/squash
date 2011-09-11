<?php
/**
 * @author Karol
 */
class FArrayHelper {

	/**
	 * zamienia klucze tabeli z camel caseowych na z podkreslnikiem
	 * 
	 * @param array $array
	 * @param boolean $withUndescorePrefix czy zwracane klucze maja byc poprzedzone podkreslnikiem
	 * @return
	 */
    public function keysFromCamelCase(array $array)
    {
    	$tmp = array();
    	$stringHelper = new FStringHelper();
    	foreach ($array as $key => $val) {
    		$newKey = $stringHelper->fromCamelCase($key);
    		$tmp[$newKey] = $val;
    	}
    	
    	return $tmp;
    	
    }
    
    /**
     * sortuje tablice obiektow wg podanego atrybutu
     * 
     * @param array $array
     * @param string $fieldName
     * @param $sortFlag (SORT_DESC, SORT_ASC)
     * @return array
     */
    public function sortByField(array $array, $fieldName, $sortFlag)
    {
    	if (empty($array)) {
    		return $array;
    	}
    	
    	$isArrayOfObjects = $this->_isArrayOfObjectsOrArrays($array);
    	$sortCol = array();
    	foreach ($array as $element) {
    		if ($isArrayOfObjects) {
    			$sortCol[] = $element->$fieldName;
    		} else {
    			if (!isset($element[$fieldName])) {
    				throw new FBaseException("one of the arrays has no field for ".$fieldName);
    			}
    			$sortCol[] = $element[$fieldName];
    		}
    	}
    	
    	array_multisort($sortCol, $sortFlag, $array);
    	
    	return $array;
    }
    
    
    /**
     * indexuje tablice wg podanego pola (indeks tablicy lub pole obiektu)
     * jesli $onlyOneValuePerKey ustawione jest na false, pole zwracanej tablicy array[$fieldName] bedzie tablica elementow
     * 
     * @param array $array
     * @param string $fieldName
     * @param boolean $onlyOneValuePerKey
     * @throws FBaseException jesli przekazano tablice tablic i jedna z nich nie ma pola z indeksem $fieldName
     * @return array
     */
    public function indexByField(array $array, $fieldName, $onlyOneValuePerKey = true)
    {
    	if (empty($array)) {
    		return array();
    	}
    	
    	$isArrayOfObjects = $this->_isArrayOfObjectsOrArrays($array);
    	$arrayCopy = array();
    	foreach ($array as $element) {
    		if ($isArrayOfObjects) {
    			$key = $element->$fieldName;
    		} else {
    			if (!isset($element[$fieldName])) {
    				throw new FBaseException("one of the arrays has no field for ".$fieldName);
    			}
    			$key = $element[$fieldName];
    		}
    		
    		if ($onlyOneValuePerKey) {
    			$arrayCopy[$key] = $element;
    		} else {
    			$arrayCopy[$key][] = $element;
    		}
    	}
    	
    	return $arrayCopy;
    }
    
    /**
     * "szacuje" czy podana tablica jest tablica obiektow lub tablic
     * sprawdzenie odbywa sie sie przez sprawdzenie typu pierwszego elementu
     * 
     * @param	array $array
     * @throws	FBaseException jesli pierwszy element nie jest ani obiektem ani tablica
     * @return	boolean
     */
    private function _isArrayOfObjectsOrArrays($array)
    {
    	//jesli pierwszy element jest tablica, to zakladamy ze wszystkie sa tablica
    	//jesli pierwszy jest obiektem, to zakladamy ze wszystkie sa obiektami
    	$firstElement = reset($array);
    	if (is_object($firstElement)) {
    		return true;
    	} elseif (is_array($firstElement)) {
    		return false;
    	} else {
    		throw new FBaseException("first argument has to be an array of arrays or objects");
    	}
    }
    
}
?>