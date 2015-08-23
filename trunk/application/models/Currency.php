<?php
class Currency extends Zend_Db_Table_Abstract {
	
	protected $_primary = "id";
	protected $_name = "currency";
	
	/**
	 * Get available currency.
	 * 
	 */
	public static function getAvailableCurrencyAsArray() {
		$table = new Currency();
		$where = $table->select()->where("enabled=?", true);
		$currencies = $table->fetchAll($where);
		$arr = array();
		foreach ($currencies as $currency) {
			$arr[$currency->id] = $currency->display_cn;
		}
		return $arr;
	}
}
?>