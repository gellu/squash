<?php
class SquashRankingStateEntity extends FEntity
{
	
	/**
	 * id gracza
	 * 
	 * @var int
	 */
	protected $_playerId;

	/**
	 * wartosc/stan rankingu
	 * 
	 * @var unknown_type
	 */
	protected $_ranking;
	
	/**
	 * data obowiazywania rankingu
	 * 
	 * @var string date
	 */
	protected $_validFor;
	
	/**
	 * data wpisania do bazy informacji o rankingu
	 * 
	 * @var string datetime
	 */
	protected $_createdAt;
	
}