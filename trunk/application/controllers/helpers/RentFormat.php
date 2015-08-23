<?php
class Zend_Controller_Action_Helper_RentFormat extends Zend_Controller_Action_Helper_Abstract {
	
	/**
	 * @param $data data in to be shown on view
	 */
	public function direct($advertisement) {
		if($advertisement->rent_measurement == "month")
		return "克朗每月";
		else
		return "克朗每日";
	}	
}
?>