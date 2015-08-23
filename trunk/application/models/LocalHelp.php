<?php
require_once 'Zend/Db/Table/Abstract.php';

class LocalHelp extends Zend_Db_Table_Abstract {
	
	protected $_name = 'localhelp';
	protected $_primary = 'id';
	
	protected $_referenceMap = array (
	'City' => array (
		'column' => 'city_id',
		'refTableClass' => 'City',
		'refColumns' => 'id'
	)
	);
	
	/**
	 * Find local help text by city.
	 * 
	 * @param $city
	 */
	public function findByCity($city) {
		$select = $this->select()->where('city_id=?', $city->id);
		return $this->fetchRow($select);
	}
}
?>