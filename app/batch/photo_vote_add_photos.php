<?php
/**
 * Created on 2010-10-04
 *
 * @author Karol T.
 * 
 */
 
 
 define("ROOT_DIR", "../..");
 require_once ROOT_DIR . "/app/config/config.php";
 
 $flite = FLite::getInstance();
 $flite->setMode(FLite::MODE_BATCH);
 
 $db = $flite->getDB();
 
 $sql = "UPDATE photo SET votes_plus = 4 WHERE id = 146";
 $db->query($sql);
?>
