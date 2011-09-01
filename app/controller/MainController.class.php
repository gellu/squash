<?php
/**
* 
*/
class MainController extends FController
{
    public function index()
    {
    	$am = FLite::getInstance()->getAuthManager();
    	if (isset($_POST['login']) && isset($_POST['pass']))
		{
			if ($am->login($_POST['login'], $_POST['pass'])) {
				$this->redirect(ROOT_WWW);
			}
		}
				
		if ($am->getCurrentUser()) {
			$this->setPage('main/home');			
		}
		
		return FController::OK;
    }
    
    public function logout()
    {
    	FLite::getInstance()->getAuthManager()->logout();
    	
    	$this->redirect(ROOT_WWW);
    	
    	return FController::OK;
    }
    
   
}





?>