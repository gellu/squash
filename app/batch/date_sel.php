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
 
 $db = $flite->getDB();
 
 echo "\n selecting...";
 
 for ($i = 1; $i <= 1000; $i++)
 {
 	$strToTime1 = strtotime('2010-04-21 12:00:31');
 	$strToTime2 = strtotime('2010-04-21 12:11:30');
 	$db->query("SELECT sum(col2) FROM dates_ts WHERE `date` > '$strToTime1'");
 	
 	if ($i % 100 == 0)
 	{
 		echo ".";
 		echo "|";
 	}
 }
 $t1 = $db->getOverallExecutionTime();
 echo "\n 10000 SELECT with TIMESTAMP in " . $t1;
 
 for ($i = 1; $i <= 1000; $i++)
 {
 	$i2 = $i*2;
 	$i3 = $i*3;
 	$db->query("SELECT sum(col2) FROM dates_dt WHERE `date` > '2010-04-21 12:00:31'");
 	
 	if ($i % 100 == 0)
 	{
 		echo ".";
 		echo "|";
 	}
 }
  $t2 = $db->getOverallExecutionTime();
 echo "\n 10000 SELECT with DATETIME in " . ($t2-$t1);
 
 
 echo "\nkarol";
 
 
?>
