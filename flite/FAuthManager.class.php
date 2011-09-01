<?php
/**
 * Created on 2010-05-10
 *
 * @author Karol T.
 * 
 */
 class FAuthManager extends FBase 
 {
 	/**
 	 * @var FSession
 	 */
 	private $_session;
 	
 	/**
 	 * dane obecnie zalogowanego usera
 	 * 
 	 * @var FUserEntity
 	 */
 	private $_currentUser = null;
 	
 	public function __construct($dbHandler, $sessHandler)
 	{
 		$this->_db = $dbHandler;
 		$this->_session = $sessHandler;
 	}
 	
 	public function login($login, $pass)
 	{
 		$userRepo = new FUserRepository();
 		$user = $userRepo->getForLogin($login, md5($pass));
		
 		if ($user) {
 			$this->_session->set('user_id', $user->id);
 			$this->_currentUser = $user;
 			return true;
 		} else {
 			return false;
 		}
 	}
 	
 	public function logout()
 	{
 		$this->_session->remove('user_id');
 		$this->_currentUser = null;
 		
 		return true;
 	}
 	
 	/**
 	 * zwraca dane obecnie zalogowanego usera
 	 * 
 	 * @throws FBaseException
 	 * @return FUserEntity
 	 */
 	public function getCurrentUser()
 	{
 		if ($this->_currentUser) {
 			return $this->_currentUser;
 		} else {
 			if ($userId = $this->_session->get('user_id')) {
 				$userRepo	= new FUserRepository();
 				$user		= $userRepo->getById($userId);
 				if ($user === null) {
 					throw new FBaseException("trying to currentUser that does not exist");
 				}
 				return $user;
 			} else {
 				return null;
 			}
 		}
 	}
 }
?>
