<?php
class FUserEntity extends FEntity
{
	/**
	 * nazwa usera
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * skrocona nazwa usera, np. inicjaly
	 * 
	 * @var string
	 */
	protected $_shortName;
	
	/**
	 * email
	 * 
	 * @var string
	 */
	protected $_email;
	
	/**
	 * md5 z hasla
	 * 
	 * @var string
	 */
	protected $_pass;
	
	/**
	 * data utworzenia profilu
	 * 
	 * @var string datetime
	 */
	protected $_createdAt;
	
	/**
	* data ostatniej modyfikacji profilu
	*
	* @var string datetime
	*/
	protected $_modifiedAt;
	
}