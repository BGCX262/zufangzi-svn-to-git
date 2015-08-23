<?php
class AdvertisingAgency extends Zend_Db_Table_Abstract {
	
	const ID = "id";
	const USER = "user_id";
	const CITY = "city_id";
	const DESCRIPTION = "description";
	const COMMENT = "comment";
	const STATUS = "status";
	const CREATED = "created";
	const MODIFIED = "modified";
	
	protected $_primary = "id";
	protected $_name = "advertising_agency";
	
	protected $_referenceMap = array (
	'User' => array (
		'columns' => 'user_id', 
		'refTableClass' => 'User', 
		'refColumns' => 'id' 
	),
	'City' => array (
		'columns' => 'city_id',
		'refTableClass' => 'City',
		'refColumns' => 'id'
	)
	);
	
	/**
	 * Add entry.
	 * 
	 * @param $data
	 * @return return added object
	 */
	public function addEntry($data) {
		$advertisingAgency = $this->findByUnique($data[self::USER], $data[self::CITY], $data[self::DESCRIPTION]);
		if (empty($advertisingAgency)) {
			$id = $this->insert($data);
			return $this->find($id)->current();
		} else {
			return $advertisingAgency;
		}
	}
	
	/**
	 * Find entry by unique constraint.
	 * 
	 * @param $userId
	 * @param $cityId
	 * @param $description
	 * @return AdvertisingAgency
	 */
	public function findByUnique($userId, $cityId, $description) {
		$where = $this->select()->where("user_id=?", $userId)->where("city_id=?", $cityId)->where("description=?", $description);
		return $this->fetchRow($where);
	}
}
?>