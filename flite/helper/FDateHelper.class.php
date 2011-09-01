<?php
/**
 * @author Karol
 */
class FDateHelper {

    public function hullDate(&$str)
    {
    	$count = 0;
    	$regex = "/(dzisiaj|today|dzis|dziś)/i";
    	$str = preg_replace($regex, '', $str, -1, $count);
    	if ($count)
    		return date("Y-m-d", time());
    		
    	$regex = "/(jutro|tommorow)/i";
    	$str = preg_replace($regex, '', $str, -1, $count);
    	if ($count)
    		return date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
    	
    	$regex = "/(pojutrze)/i";
    	$str = preg_replace($regex, '', $str, -1, $count);
    	if ($count)
    		return date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")));
    		
    	$regex = "/(anytime)/i";
    	$str = preg_replace($regex, '', $str, -1, $count);
    	if ($count)
    		return null;	
    	
    	return null;
    }
}
?>