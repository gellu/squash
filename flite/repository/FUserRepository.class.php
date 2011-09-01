<?php
class FUserRepository extends FRepository
{
	/**
	 * pobiera usera po emailu i hasle
	 * 
	 * @param string $email
	 * @param string $pass md5
	 */
	public function getForLogin($email, $pass)
	{
		$sql = "SELECT * FROM ".$this->_getTableName()." WHERE email = '".$this->_db->escape($email)."' AND pass = '".$this->_db->escape($pass)."'";
		$data = $this->_db->getRow($sql);
		if ($data === null) {
			return null;
		} else {
			return new FUserEntity($data);
		}
		
	}	
}