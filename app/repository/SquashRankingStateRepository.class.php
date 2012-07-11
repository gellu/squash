<?php
class SquashRankingStateRepository extends FRepository
{
	/**
	 * zwraca informacje o ostatnim rankingu graczy przed podana data
	 * zwraca tablice zaindeksowana wg id gracza
	 * 
	 * @param string $date
	 * @return array
	 */
	public function getLatestBefore($date)
	{
		$sql = "SELECT srs.* 
				FROM ".$this->_getTableName()." srs
				JOIN (
					SELECT id, player_id, MAX(valid_for) max_date
					FROM  `".$this->_getTableName()."` 
					GROUP BY player_id
					) max_srs ON max_srs.player_id = srs.player_id
				WHERE srs.valid_for <'".$date."' AND srs.valid_for = max_srs.max_date";

		$data = $this->_db->getResults($sql);
		if (!$data) {
			return null;
		}
		
		$ret = array();
		foreach ($data as $row) {
			$ret[$row['player_id']] = new SquashRankingStateEntity($row);
		}
		
		return $ret;
	}
	
	/**
	 * zwraca maxymalna date, dla ktorej wyznaczany byl ranking gracza
	 * 
	 * @return
	 */
	public function getMaxDate()
	{
		$sql = "SELECT MAX(valid_for) FROM  `".$this->_getTableName()."` ";
		return $this->_db->getVar($sql);
	}

	/**
	 * zwraca ostatni ranking w tablicy zindeksowanej po id gracza
	 *
	 * @return array|null
	 */
	public function getLastRank()
	{
		$sql = "SELECT valid_for, ranking, player_id
				FROM ".$this->_getTableName()."
				WHERE valid_for = (SELECT MAX(valid_for) FROM ".$this->_getTableName()." WHERE valid_for < (SELECT MAX(valid_for) FROM ".$this->_getTableName()."))";

		$data = $this->_db->getResults($sql);
		if (!$data) {
			return null;
		}

		$ret = array();
		foreach ($data as $row) {
			$ret[$row['player_id']] = new SquashRankingStateEntity($row);
		}

		return $ret;
	}

}