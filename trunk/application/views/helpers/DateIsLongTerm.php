<?php
class Zend_View_Helper_DateIsLongTerm {
	
	protected $_view;
	function setView($view) { 
		$this->_view = $view; 
	}
	
	function dateIsLongTerm($dateTime) {
		return $dateTime == Constant::DEFAULT_CHECKOUT_DATE;
	}
}
?>