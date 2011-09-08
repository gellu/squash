<?php
class SquashPlayerEntity extends FEntity
{
	/**
	 * ranking gracza
	 */
	protected $_ranking;
	
	protected $_user;
	
	protected $_createdAt;
	
	
	public function getUser()
	{
		if ($this->_user === null) {
			$repo	= new FUserRepository();
			$this->_user = $repo->getById($this->_id);
		} 
		
		return $this->_user;
	}
	
	public function setUser(FUserEntity $user)
	{
		$this->_user	= $user;
		$this->_id		= $user->id;
	}
}