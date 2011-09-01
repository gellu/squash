<?php
/**
* 
*/
class SquashController extends FController
{
    public function index()
    {
            $am	  = FLite::getInstance()->getAuthManager();
			$user = $am->getCurrentUser();	

			
            return FController::OK;
    }
}





?>