<?php
require_once 'Zend/Db/Table/Abstract.php';

class Message extends Zend_Db_Table_Abstract {
	
	protected $_name = 'message';
	protected $_primary = 'id';
	
	protected $_referenceMap = array (
	'User' => array (
		'columns' => 'user_id', 
		'refTableClass' => 'User', 
		'refColumns' => 'id' 
	)
	);
	
	/**
	 * Get user of given message.
	 * 
	 * @param $id message id
	 * @return return user
	 */
	public static function getUser($id) {
		$message = new Message();
		$message = $message->find($id)->current();
		return $message->findParentRow ('User');
	}
	
	/**
	 * Add a new message.
	 * 
	 * @param $data
	 */
	public function postMessage($data) {
		$id = $this->insert($data);
	}
	
	/**
	 * Find all active message, sorted by created, desc.
	 */
	public function findAllActive() {
		$select = $this->select()->where('status=?', MessageStatus::ACTIVE)
		->where('type=?', MessageType::GUEST_BOOK)
		->order('created DESC');
		return $this->fetchAll($select);
	}
}
?>