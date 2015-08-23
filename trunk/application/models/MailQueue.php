<?php
require_once 'Zend/Db/Table/Abstract.php';

class MailQueue extends Zend_Db_Table_Abstract {
	
	protected $_name = 'mail_queue';
	protected $_primary = 'id';
	
	/**
	 * Add email to queue.
	 * 
	 * @param $type
	 * @param $name
	 * @param $subject
	 * @param $sender
	 * @param $recipient
	 * @param $message
	 * @param $created
	 */
	public function addToQueue($type, $name, $subject, $sender, $recipient, $message, $created) {
		$obj = $this->createRow();
		$obj->type = $type;
		$obj->name = $name;
		$obj->subject = $subject;
		$obj->sender = $sender;
		$obj->recipient = $recipient;
		$obj->message = $message;
		$obj->sent = false;
		$obj->created = $created;
		$obj->ipaddress = $this->getIpAddress();
		$id = $obj->save();
		return $id;
	}
	
	/**
	 * Get sender's ipaddress.
	 */
	private function getIpAddress() {
		$ipaddress = null;
		if (isset($_SERVER["REMOTE_ADDR"])) {
		    $ipaddress = $_SERVER["REMOTE_ADDR"];
		} else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		    $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
		    $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
		}
		return $ipaddress;
	}
	
	/**
	 * Find all unsent mail.
	 * 
	 * @return return all unsent mail
	 */
	public function findUnsentMails() {
		$select = $this->select()->where('sent=?', false);
		return $this->fetchAll($select);
	}
	
	/**
	 * Update mail status.
	 * 
	 * @param $id mail id
	 * @param $failMessage faile message, if any
	 * @param $sendTime
	 */
	public function updateStatus($id, $failMessage, $sendTime) {
		$obj = $this->find($id)->current();
		$obj->sent = true;
		$obj->error_message = $failMessage;
		$obj->send_time = $sendTime;
		$obj->save();
	}
}
?>