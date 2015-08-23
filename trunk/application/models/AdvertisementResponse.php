<?php
class AdvertisementResponse extends Zend_Db_Table_Abstract {
	
	protected $_primary = 'id';
	protected $_name = 'advertisement_response';
	
	protected $_referenceMap = array (
	'Advertisement' => array(
		'columns' => 'advertisement_id',
		'refTableClass' => 'Advertisement',
		'refColumns' => 'id'
	),
	'User' => array (
		'columns' => 'user_id', 
		'refTableClass' => 'User', 
		'refColumns' => 'id' 
	),
	'Mail' => array (
		'columns' => 'mail_id',
		'refTableClass' => 'MailQueue',
		'refColumns' => 'id'
	)
	);
	
	/**
	 * Add advertisement response.
	 * 
	 * @param $advertisemnt_id
	 * @param $user_id
	 * @param $mail_id
	 */
	public function addAdvertisementResponse($advertisemnt_id, $user_id, $mail_id) {
		$obj = $this->createRow();
		$obj->advertisement_id = $advertisemnt_id;
		$obj->user_id = $user_id;
		$obj->mail_id = $mail_id;
		$obj->save();
	}
}
?>