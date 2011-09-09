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
    public function keysFromCamelCase(array $array, $withUndescorePrefix = true)
    {
    	$tmp = array();
    	$stringHelper = new FStringHelper();
    	foreach ($array as $key => $val) {
    		$newKey = $stringHelper->fromCamelCase($key);
    		$tmp[$newKey] = $val;
    	}
    	
    	return $tmp;
    	
    }
}
?>