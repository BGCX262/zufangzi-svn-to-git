<?php
class Zend_Controller_Action_Helper_DateIsValid extends Zend_Controller_Action_Helper_Abstract {
	
	/**
	 * @param $data data in to be shown on view
	 */
	public function direct($dateTime) {
		
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $dateTime, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    return false;
	}	   
	}	
}
?>