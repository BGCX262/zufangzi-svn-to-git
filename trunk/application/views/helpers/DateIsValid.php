<?php
class Zend_View_Helper_DateIsValid {
	protected $_view;
	function setView($view) { 
		$this->_view = $view; 
	} 
	
	function dateIsValid($dateTime) {
	   
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $dateTime, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    return false;
	}	   
	}
}
?>