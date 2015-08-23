<?php
class Category extends Zend_Db_Table_Abstract {
	
	const APARTMENT = 1;
	const SHOP = 2;
	
	protected $_primary = "id";
	protected $_name = "category";
	
	protected $_dependentTables = array(
		'Advertisement'
	);
}
?>