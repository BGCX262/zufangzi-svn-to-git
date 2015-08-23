<?php
class Zend_View_Helper_RentFormat {
	protected $_view;
	function setView($view) { 
		$this->_view = $view; 
	} 
	
	function rentFormat($advertisement) {
		
		$str = Advertisement::getCurrency($advertisement)->display_cn;
		
		if($advertisement->rent_measurement == "month")
		return  $str."每月";
		else
		return  $str."每日";		
	}
}
?>