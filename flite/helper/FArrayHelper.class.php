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
    	//jesli pierwszy element jest tablica, to zakladamy ze wszystkie sa tablica
    	//jesli nie, to zakladamy ze wszystkie sa obiektami
    	$arrayOfObjects = is_array(reset($array)) ? false : true;
    	$firstElement = reset($array);
    	
    	if (is_object($firstElement)) {
    		$arrayOfObjects = true;
    	} elseif (is_array($firstElement)) {
    		$arrayOfObjects = false;
    	} else {
    		throw new FBaseException("first argument has to be an array of arrays or objects");
    	}
    	
    	
    	$sortCol = array();
    	foreach ($array as $element) {
    		if ($arrayOfObjects) {
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
    
    
    public function indexObjectsByField(array $array, $fieldName, $allowManyValuesToOneKey = false)
    {
    	
    }
    
}
?>