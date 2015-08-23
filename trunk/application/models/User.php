<?php
require_once 'Zend/Db/Table/Abstract.php';

class User extends Zend_Db_Table_Abstract {
	
	const NAME = "name";
	const EMAIL = "email";
	const MOBILE = "mobile";
	
	protected $_name = 'user';
	protected $_primary = 'id';
	
	protected $_dependentTables = array(
		'Advertisement',
		'Message',
		'AdvertisementResponse'
	);
	
	/**
	 * Add user, if user is already exist, then just return it.
	 * 
	 * @param $data
	 * @return return user
	 */
	public function addUser($data) {
		$user = $this->findByUnique($data['name'], $data['email'], $data['mobile']);
		if (!isset($user)) {
			$user = $this->createRow($data);
			$user->save();
		}
		return $user;
	}
	
	/**
	 * Find user by unique.
	 * 
	 * @param $name
	 * @param $email
	 * @param $mobile
	 */
	public function findByUnique($name, $email, $mobile) {
		$select = $this->select()->where('upper(name)=upper(?)', trim($name))
		->where('upper(email)=upper(?)', trim($email));
		if (empty($mobile) || !isset($mobile)) {
			$select = $select->where('mobile is null OR mobile = ""');	
		} else {
			$select = $select->where('mobile=?', $mobile);
		}
		return $this->fetchRow($select);
	}
}
?>