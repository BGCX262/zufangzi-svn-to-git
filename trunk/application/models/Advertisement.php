<?php
require_once 'Zend/Db/Table/Abstract.php';

class Advertisement extends Zend_Db_Table_Abstract {
	
	const WANT = 'want';
	const LEASE = 'lease';
	const RENT_MONTHLY = 'month';
	const RENT_DAILY = 'day';
	
	protected $_name = 'advertisement';
	protected $_primary = 'id';
	
	protected $_dependentTables = array(
		'AdvertisementResponse',
	    'AdvertisementExtension',
	    'AdvertisementNotification'
	);
	
	protected $_referenceMap = array (
	'User' => array (
		'columns' => 'user_id', 
		'refTableClass' => 'User', 
		'refColumns' => 'id' 
	),
	'City' => array (
		'columns' => 'city_id',
		'refTableClass' => 'City',
		'refColumns' => 'id'
	),
	'Category' => array(
		'columns' => 'category_id',
		'refTableClass' => 'Category',
		'refColumns' => 'id'
	),
	'Currency' => array(
		'columns' => 'currency',
		'refTableClass' => 'Currency',
		'refColumns' => 'id'
	)
	);
	
	/**
	 * Add or update advertisement.
	 * if id is include in the $data, then update
	 * otherwise, add new entry.
	 * 
	 * @param $data
	 */
	public function addOrUpdateAdvertisement($data) {
		if (isset($data['id'])) {
			$adv = $this->findById($data['id']);
			$adv->title = $data['title'];
			if (isset($data['description'])) {
				$adv->description = $data['description'];
			}
			if (isset($data['type'])) {
				$adv->type = $data['type'];
			}
			$adv->user_id = $data['user_id'];
			if (isset($data['area'])) {
				$adv->area = $data['area'];
			}
			if (isset($data['address'])) {
				$adv->address = $data['address'];
			}
			if (isset($data['rent'])) {
				$adv->rent = $data['rent'];
			}
			$adv->rent_measurement = $data['rent_measurement'];
			$adv->num_of_room = $data['num_of_room'];
			$adv->start_date = $data['start_date'];
			$adv->stop_date = $data['stop_date'];
			$adv->modified = $data['modified'];
			$adv->save();
		} else {
			$adv = $this->createRow($data);
			$id = $adv->save();
			$adv = $this->findById($id);
		}
		return $adv;
	}
	
	/**
	 * Find advertisement by id.
	 * 
	 * @param $id
	 * @return return Advertisement
	 */
	public function findById($id) {
		return $this->find($id)->current();
	}
	
	/**
	 * Get user.
	 * 
	 * @param id advertisement id
	 */	
	public static function getUser($id) {
		$obj = new Advertisement();
		$advertisement = $obj->findById($id);
		return $advertisement->findParentRow ('User');
	}
	
	/**
	 * Get city.
	 * 
	 * @param id advertisement id
	 */	
	public static function getCity($id) {
		$obj = new Advertisement();
		$advertisement = $obj->findById($id);
		return $advertisement->findParentRow ('City');
	}
	
	/**
	 * Get category.
	 * 
	 * @param $advertisement
	 * @return return advertisement's category
	 */
	public static function getCategory($advertisement) {
		return $advertisement->findParentRow('Category');
	}
	
	/**
	 * Get currency.
	 * 
	 * @param $advertisement
	 * @return return advertisement's currency
	 */
	public static function getCurrency($advertisement) {
		return $advertisement->findParentRow('Currency');
	}
	
	/**
	 * Find advertis by password, password is unique in the table.
	 * 
	 * @param $password
	 * @return return Advertisement object
	 */
	public function findAdvertisementByPassword($password) {
		$select = $this->select()->where('password=?', $password);
		return $this->fetchRow($select);
	}
	
	/**
	 * Find advertisement by city, include active and closed post.
	 * 
	 * @param $city
	 * @return return array of Advertisement
	 */
	public function findByCity($city) {
		$select = $this->select()->where('status = "'.PostStatus::CLOSED.'" OR status = "'.PostStatus::ACTIVE.'"')
			->where('city_id=?', $city->id)
			->order('created desc');
		return $this->fetchAll($select);
	}
	
	/**
	 * Close advertisement.
	 * @param $id
	 */
	public function closeAdvertisement($id) {
		$advertisement = $this->findById($id);
		$advertisement->status = PostStatus::CLOSED;
		$advertisement->save();
	}
	
	/**
	 * Delete advertisement.
	 */
	public function deleteAdvertisement($id) {
		$advertisement = $this->findById($id);
		$advertisement->status = PostStatus::DELETED;
		$advertisement->save();
	}
	
	/**
	 * Search by criteria data.
	 * 
	 * @param $data
	 * @return return search result
	 */
	public function searchByCriteria($data) {
		$checkin_date = $data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE];
		$checkout_date = $data[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE];
		if (empty($checkout_date)) {
			$checkout_date = Constant::DEFAULT_CHECKOUT_DATE;
		}
		
		$select = $this->select()->from($this, 
			array(
			'DATEDIFF("'.$checkout_date.'", "'.$checkin_date.'") / DATEDIFF(stop_date, start_date) as diff', 
			'id', 'status', 'created', 'type', 'category_id', 'title', 'rent', 'rent_measurement', 'start_date', 'stop_date', 'area', 'address', 'description', 'city_id', 'country', 'district', 'num_of_room'
			)
		);
		if ($data['type'] != 'all') {
			$select = $select->where('type=?',$data['type']);
		}
		if (!empty($data['keyword'])) {
			$select = $select->where('upper(title) like "%'.addslashes(strtoupper($data['keyword'])).'%" OR upper(description) like "%'.addslashes(strtoupper($data['keyword'])).'%"');
		}
		if (!empty($data['rent'])) {
			$select = $select->where('rent>=?', $data['rent']);
			if (!empty($data['rent_measurement'])) {
				$select = $select->where('rent_measurement=?', $data['rent_measurement']);
			}
		}
//		if (!empty($data['start_date'])) {
//			$select = $select->where('start_date>="'.$data['start_date'].'" OR (start_date<"'.$data['start_date'].'" AND (stop_date>="'.$data['start_date'].'" OR stop_date="0000-00-00" OR stop_date='.Constant::DEFAULT_CHECKOUT_DATE.'))');
//		}
		if (!empty($data['city'])) {
			$select = $select->where('city_id = ?', $data['city']);
		}
		
		if (!empty($data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE])) {
			$select = $select->where('status = "'.PostStatus::ACTIVE.'"');
		} else {
			if (!empty($data['status'])) {
				$select = $select->where('status = "'.PostStatus::CLOSED.'" OR status = "'.PostStatus::ACTIVE.'"');
			}
		}
//		if (!empty($data['search_business']) && $data['search_business'] == 1) {
//			$select = $select->where('category_id = ?', Category::SHOP);
//		}
		if (!empty($data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE])) {
			if ($data['type'] == 'want') {
				$select = $select->where('DATE(start_date)>=?', $data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE]);
			} else {
				$select = $select->where('DATE(start_date)<=?', $data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE]);
			}	
		}
		if (!empty($data[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE])) {
			if ($data['type'] == 'want') {
				$select = $select->where('DATE(stop_date)<=?', $data[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE]);
			} else {
				$select = $select->where('DATE(stop_date)>=?', $data[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE]);
			}
		} else {
			if (!empty($data[Constant::VAR_SEARCH_DATA_CHECKIN_DATE])) {
				$select = $select->where('stop_date IS NULL OR stop_date=0000-00-00 OR stop_date="'.Constant::DEFAULT_CHECKOUT_DATE.'"');
			}
		}
		
		// if fill in checkin/checkout date, then sort by date diff
		if (!empty($checkin_date)) {
			$select->order('diff DESC');
		}
		
		if (!empty($data['sortedBy'])) {
			if ($data['sortedBy'] == 'created') {
				$select = $select->order('created desc');
			} else if ($data['sortedBy'] == 'rent') {
				$select = $select->order('rent asc');
			}
		} else {
			$select = $select->order('created desc');
		}
		return $this->fetchAll($select);
	}
	
	/**
	 * Find all advertisments which need to send notification.
	 * 
	 * @param $data
	 */
	public function findAdvertisementToSendNofication($date) {
		$where = $this->select()->where('modified <= ?', $date)
		->where('status = ?', PostStatus::ACTIVE);
		return $this->fetchAll($where);
	}
	
	/**
	 * Find all advertisements to be closed, if stop_date is older than current date, then marked it as closed.
	 */
	public function findAdvertisementToClose() {
		$where = $this->select()
		->where("DATE(stop_date)<=?", date("Y-m-d"))
		->where("status=?", PostStatus::ACTIVE);
		return $this->fetchAll($where);
	}
	
	/**
	 * 
	 * @param $keywords invalid keyword to be checked aginst
	 */
	public function updateSpamBulletins($keyword) {
		$data = array('status' => PostStatus::SPAM);
		return $this->update($data, array('description like ?'=> '%'.$keyword.'%', 'status=?'=>PostStatus::ACTIVE));
//		$sub_select = $this->_db->select()
//		       ->from('advertisement', array('id'))
//		       ->where('description like ?', '%'.$keyword.'%');
//		
//		$updated_rows = $this->_db->update('advertisement', 
//		        array('status' => PostStatus::SPAM),
//		        "id IN ($sub_select)" 
//		    );
//		return $updated_rows;
	}
}
?>