<?php
require_once 'Zend/Db/Table/Abstract.php';

class City extends Zend_Db_Table_Abstract {
	
	protected $_name = 'city';
	protected $_primary = 'id';
	
	protected $_dependentTables = array(
		'Advertisement'
	);
	
	public function findById($id) {
		return $this->find($id)->current();
	}
	
	/**
	 * Find city by name.
	 * 
	 * @param $name
	 */
	public function findByName($name) {
		$select = $this->select()->where('upper(name) = upper(?)', $name);
		return $this->fetchRow($select);
	}
}
?>