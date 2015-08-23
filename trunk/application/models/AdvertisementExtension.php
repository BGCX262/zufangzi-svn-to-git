<?php
class AdvertisementExtension extends Zend_Db_Table_Abstract {
	
	protected $_primary = 'id';
	protected $_name = 'advertisement_extension';
	
	protected $_referenceMap = array (
    'Advertisement' => array (
        'columns' => 'advertisement_id', 
        'refTableClass' => 'Advertisement', 
        'refColumns' => 'id' 
    ));
}
?>