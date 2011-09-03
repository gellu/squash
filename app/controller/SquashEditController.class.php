<?php
/**
* 
*/
class SquashEditController extends FController
{
	public function __construct()
	{
		parent::__construct();
		$this->_squashResultRepo = new SquashResultRepository();
		$this->_usersRepo = new FUserRepository();
	}
	
	public function addResult()
	{
		
	}
}





?>