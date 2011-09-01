<?php
/**
 * Created on 2010-04-21
 *
 * @author Karol T.
 * 
 */
 
 define("ROOT_DIR", "../..");
 require_once ROOT_DIR . "/app/config/config.php";
 
 $flite = FLite::getInstance();
 $flite->setMode(FLite::MODE_BATCH);
 
 $equation = '$currResult = 1000 * pow(1+$x/100/12,9) + 300 * pow(1+$x/100/12,7) + 1000 * pow(1+$x/100/12,4);';
 //$equation = '$currResult = $x/100*1000;';
 $result = 2397.4;
 //$result = 12.4;
 
 $equation = '$currResult = 6000 * pow(1+$x/100/12,9) + 3100 * pow(1+$x/100/12,7) + 5000 * pow(1+$x/100/12,6) + 5500 * pow(1+$x/100/12,4) + 2000;';
 $result = 22764;
 
 
 
 $threshold = 0.5;
 $min = 0;
 $max = 100;
 
 $currDiff = $result;
// 
// $x = 5;
// eval($equation);
// var_dump($currResult);
// 
// $x = 2;
// eval($equation);
// var_dump($currResult);
 $i = 0;
 while (abs($currDiff) > $threshold)
 {
 	$x = ($min + $max)/2;
 	eval($equation);
 	$currDiff = $currResult - $result;
 	echo "\n$currResult";
 	echo "\n$min $max $x $currDiff";
 	$currDiff > 0 ? $max = $x : $min = $x;
 	
 	$i++;
 	
 	//if ($i == 5) exit;   
 }
 
 echo "\n\ninterest is $x, calculated in $i loops";
 
 
 
 
?>
