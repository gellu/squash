<?php
class SquashResultRepository extends FRepository
{
	/**
	 * zwraca date ostatniego meczu
	 * 
	 * @throws FRepositoryException
	 * @return string date
	 */
	public function getLastPlayedDate()
	{
		$sql	= "SELECT max(played_at) FROM ".$this->_getTableName()."";
		$date	= $this->_db->getVar($sql);
		
		if ($date === null) {
			throw new FRepositoryException("cant find max played_at for results");
		}
		
		return $date;
	}
	
	/**
	 * pobiera daty rozegranych meczy sasiadujace z podana data (poprzednia, nastepna) 
	 * 
	 * @param string $date
	 * @return array tablica postaci(prevDate, nextDate) jesli znie znajdzie sasiadujacej daty prevDate lub nextDate moga byc ustawione na null
	 */
	public function getNeighbouringDates($date)
	{
		$sql = "SELECT max(played_at) FROM ".$this->_getTableName()." WHERE played_at < '".$date."'";
		$prevDate = $this->_db->getVar($sql);
		
		$sql = "SELECT min(played_at) FROM ".$this->_getTableName()." WHERE played_at > '".$date."'";
		$nextDate = $this->_db->getVar($sql);
		
		return array($prevDate, $nextDate);

	}
	
	/**
	 * zwraca wszystkie daty rozegrania meczy posortowane rosnaco
	 * 
	 * @return array
	 */
	public function getAllDates()
	{
		$sql = "SELECT distinct(played_at) FROM ".$this->_getTableName()." ORDER BY played_at ASC";
		
		return $this->_db->getCol($sql);
	}
}