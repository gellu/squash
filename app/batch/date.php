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
 
 echo "\n inserting...";
 
 for ($i = 1; $i <= 10000; $i++)
 {
 	$i2 = $i*2;
 	$i3 = $i*3;
 	$db->query("INSERT INTO dates_ts (col1,col2,col3) VALUES ($i, $i2, $i3)");
 	
 	if ($i % 1000 == 0)
 	{
 		echo ".";
 		sleep(1);
 		echo "|";
 	}
 }
 $t1 = $db->getOverallExecutionTime();
 echo "\n10000 rows with TIMESTAMP inserted in " . $t1;
 
 for ($i = 1; $i <= 10000; $i++)
 {
 	$i2 = $i*2;
 	$i3 = $i*3;
 	$db->query("INSERT INTO dates_dt (date, col1,col2,col3) VALUES (NOW(), $i, $i2, $i3)");
 	
 	if ($i % 1000 == 0)
 	{
 		echo ".";
 		sleep(1);
 		echo "|";
 	}
 }
  $t2 = $db->getOverallExecutionTime();
 echo "\n10000 rows with DATETIME inserted in " . ($t2-$t1);
 
 
 echo "\nkarol";
 
 
?>
