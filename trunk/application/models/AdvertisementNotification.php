<?php
class AdvertisementNotification extends Zend_Db_Table_Abstract {
	
	protected $_primary = 'id';
	protected $_name = 'advertisement_notification';
	
	protected $_referenceMap = array (
    'Advertisement' => array (
        'columns' => 'advertisement_id', 
        'refTableClass' => 'Advertisement', 
        'refColumns' => 'id' 
    ));
	
	/**
	 * Insert new entry to notification list if it's new.
	 * 
	 * @param $data
	 */
	public function addOneEntry($data) {
		$notification = $this->findByAdvertisementTypeAndStatus($data["advertisement_id"], $data["type"], $data["status"]);
		if (empty($notification)) {
			$this->insert($data);
		}
	}
	
	/**
	 * Find advertisement notification by id, type and status.
	 * 
	 * @param $advertisement_id
	 * @param $type
	 * @param $status
	 */
	public function findByAdvertisementTypeAndStatus($advertisement_id, $type, $status) {
		$where = $this->select()->where("advertisement_id=?", $advertisement_id)
		->where("type=?", $type)->where("status=?", $status);
		return $this->fetchRow($where);
	}
}
?>