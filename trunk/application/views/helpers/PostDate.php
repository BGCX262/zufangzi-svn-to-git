<?php
class Zend_View_Helper_PostDate {
	protected $_view;
	function setView($view) { 
		$this->_view = $view; 
	} 
	
	function postDate($dateTime) {
		if (strtotime(date("Y-m-d",strtotime($dateTime))) == strtotime(date("Y-m-d"))) {
			return "今天 ".date("H:i",strtotime($dateTime));
		} else 
		if (strtotime(date("Y-m-d", mktime(0, 0, 0, date("m"),date("d")-1,date("Y"))))== strtotime(date("Y-m-d",strtotime($dateTime)))) {
			return "昨天 ".date("H:i",strtotime($dateTime));
		} else 
		if (strtotime(date("Y-m-d", mktime(0, 0, 0, date("m"),date("d")-2,date("Y"))))== strtotime(date("Y-m-d",strtotime($dateTime)))) {
			return "前天 ".date("H:i",strtotime($dateTime));
		} else 		
		if (date("Y",strtotime($dateTime)) == date("Y"))
		return date("n-j H:i",strtotime($dateTime));
		else
		return date("Y n-j",strtotime($dateTime));
		
	}
}
?>