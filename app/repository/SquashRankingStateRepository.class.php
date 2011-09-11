<?php
class SquashRankingStateRepository extends FRepository
{
	/**
	 * zwraca informacje o ostatnim rankingu graczy przed podana data
	 * 
	 * @param string $date
	 * @return array
	 */
	public function getLatestBefore($date)
	{
		$sql = "SELECT max(valid_for) FROM ".$this->_getTableName()." WHERE valid_for < '".$date."'";
		$maxDate = $this->_db->getVar($sql);
		if (!$maxDate) {
			return null;
		}
		
		return $this->getAllBy(array('validFor' => $maxDate));
	}
}