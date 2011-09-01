<?php
/**
 * @author Karol
 */
class FStringHelper {

    public function escape($str)
    {
    	return htmlspecialchars($str, ENT_QUOTES);
    }
    
    public function trimBetter($str)
    {
    	$str = preg_replace('/^([\s\t,\']*)?([\w\/_-\s]*)([\s\t,\']*)?$/i', '$2', $str);
    	$str = trim($str);
    	return $str;
    }
    
    /**
     * przerabia string camelCasem na string z_underlinem
     * 
     * @param string $str
     */
    public function fromCamelCase($str) {
    	$str[0] = strtolower($str[0]);
    	$func = create_function('$c', 'return "_" . strtolower($c[1]);');
    	return preg_replace_callback('/([A-Z])/', $func, $str);
    }
    
    /**
     * zamienia tekst _z_podkreslnikami na camelCase
     * 
     * @param string $str
     * @return string
     */
    public function toCamelCase($str) {
    	//wycinamy ew. podkreslenie na poczatku
    	if (substr($str, 0 , 1) == '_') {
    		$str = substr($str, 1);
    	}
    	
    	$func = create_function('$c', 'return strtoupper($c[2]);');
    	return preg_replace_callback('/(_([a-z]))/', $func, $str);
    }
}
?>