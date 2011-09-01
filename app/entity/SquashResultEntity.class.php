<?php
class SquashResultEntity extends FEntity
{
	/**
	 * id pierwszego gracza
	 * 
	 * @var int
	 */
	protected $_playerOneId;
	
	private $_playerOne;
	private $_playerTwo;
	
	/**
	 * id drugiego gracza
	 * 
	 * @var int
	 */
	protected $_playerTwoId;
	
	/**
	 * wynik pierwszego gracza
	 * 
	 * @var int`
	 */
	protected $_scoreOne;
	
	/**
	 * wynik drugiego gracza
	 * 
	 * @var int
	 */
	protected $_scoreTwo;
	
	/**
	 * data rozegrania meczu
	 * 
	 * @var string date
	 */
	protected $_playedAt;
	
	/**
	 * data wpisania do bazy informacji o meczu
	 * 
	 * @var string datetime
	 */
	protected $_createdAt;
	
}