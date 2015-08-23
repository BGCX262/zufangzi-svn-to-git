<?php

class BulletinController extends Zend_Controller_Action {
	const MAX_TRIES = 10;
	
	private $advertisement;
	private $advertisingAgency;
	private $user;
	private $mailQueue;
	private $advertisementResponse;
	private $city;
	private $localHelp;
	
	public function init() {
		$this->advertisement = new Advertisement ( );
		$this->advertisingAgency = new AdvertisingAgency();
		$this->user = new User ( );
		$this->mailQueue = new MailQueue ( );
		$this->advertisementResponse = new AdvertisementResponse ( );
		$this->city = new City ( );
		$this->localHelp = new LocalHelp ( );
		
		$this->initView();
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');		 
		$this->view->flashmsgs = $this->_flashMessenger->getMessages();
	}
	
	public function indexAction() {
		// action body
	}
	
	/**
	 * Create post
	 */
	public function createAction() {
		$config = Zend_Registry::get ( 'config' );
		$this->view->headLink()->prependStylesheet( $config->baseurl . '/css/datePicker.css' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/date.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.datePicker.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/create.js' );
		$this->view->class = "post";
		
		$city = $this->_getParam ( 'city' );
		$cityObj = $this->city->findByName ( $city );
		if (! isset ( $cityObj )) {
			throw new Exception ( '暂时不支持所在城市!' );
		}
		$form = new CreateOrUpdatePost ( null, null, $cityObj );
		$this->view->form = $form;
		$this->processFormData ( $form, null );
		
	}
	
	/**
	 * Update advertisement action
	 */
	public function updateAction() {
		$id = $this->_getParam ( 'id' );
		$password = $this->_getParam ( 'password' );
		$city = $this->_getParam ( 'city' );
		$cityObj = $this->city->findByName ( $city );
		if (! isset ( $cityObj )) {
			throw new Exception ( '暂时不支持所在城市!' );
		}
		$advertisement = $this->advertisement->findById ( $id );
		if (isset ( $advertisement )) {
			$form = new CreateOrUpdatePost ( $advertisement, $password, $cityObj );
			$this->view->form = $form;
			$this->processFormData ( $form, $advertisement );
		} else {
			throw new Exception ( 'Unable to find the advertisement!' );
		}
	}
	
	/**
	 * Process form data. Update and create new post.
	 * 
	 * @param $form
	 * @param $advertisement
	 * @param $city
	 */
	private function processFormData($form, $advertisement) {
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $_POST )) {
//				$isValid = $this->_helper->common->validReCaptcha ( $this->_getAllParams() );
//				if ($isValid) {
					$password = $form->getValue ( 'password' );
					if ($password != $advertisement->password) {
						throw new Exception ( '没有权限修改该帖子' );
					}
					$city_id = $form->getValue ( 'city' );
					$id = $form->getValue ( 'id' );
					$name = $form->getValue ( 'name' );
					$email = $form->getValue ( 'email' );
					$mobile = $form->getValue ( 'mobile' );
					$type = $form->getValue ( 'type' );
					$title = $form->getValue ( 'title' );
					$address = $form->getValue ( 'address' );
					$rent = $form->getValue ( 'rent' );
					$rent_measurement = $form->getValue ( 'rent_measurement' );
					$area = $form->getValue ( 'area' );
					$numOfRomm = $form->getValue ( 'num_of_room' );
					$start_date = $form->getValue ( 'start_date' );
					if (empty($start_date)) {
						throw new Exception("您必须填写入住日期");
					}
					$stop_date_tmp = $form->getValue ( 'stop_date' );
					$stop_date = empty($stop_date_tmp) ? Constant::DEFAULT_CHECKOUT_DATE : $stop_date_tmp;
					$description = $form->getValue ( 'description' );
					$category = $form->getValue('isBusiness') == 1 ? Category::SHOP : Category::APARTMENT;
					$currency = $form->getValue('currency');
					if (!isset($currency) || $currency == 0) {
						throw new Exception("您必须选择货币");
					}
					
					$db = Zend_Registry::get ( 'db' );
					$db->beginTransaction ();
					$data = array ('name' => trim ( $name ), 'email' => trim ( $email ), 'mobile' => trim ( $mobile ) );
					$user = $this->user->addUser ( $data );
					$data = array ('user_id' => $user->id, 'title' => $title, 'description' => isset ( $description ) ? $description : null, 'area' => isset ( $area ) ? $area : 0, 'address' => isset ( $address ) ? $address : null, 'rent' => isset ( $rent ) ? $rent : 0, 'rent_measurement' => $rent_measurement, 'type' => $type, 'num_of_room' => isset ( $numOfRomm ) ? $numOfRomm : 0, 'start_date' => $start_date, 'stop_date' => $stop_date, 'city_id' => $city_id, 'created' => $this->_helper->generator->generateCurrentTime (), 'modified' => $this->_helper->generator->generateCurrentTime (), "category_id" => $category, "currency"=>$currency );
					if (isset ( $advertisement )) {
						$data ['id'] = $advertisement->id;
						$this->advertisement->addOrUpdateAdvertisement ( $data );
					} else {
						$data ['password'] = $this->generatePassword ( $email );
						$advertisement = $this->advertisement->addOrUpdateAdvertisement ( $data );
						// send email to creator
						$message = MailTemplate::getCreationEmailMessage ( $advertisement );
						$this->mailQueue->addToQueue ( MailType::USER, null, MailTemplate::SUBJECT_CREATE_POST, Constant::SYSTEM_MAIL, $email, $message, $this->_helper->generator->generateCurrentTime () );
					}
					$db->commit ();
		
        			$this->_flashMessenger->addMessage("您的帖子已经成功发布。如果在十分钟内未收到系统邮件，请确认它未被归入您的垃圾邮件 -_-|| 谢谢！");
					$this->_redirect ( '/' . strtolower ( $this->city->findById ( $city_id )->name ) . '/bulletin/list' );
//				}
			}
		}
	}
	
	/**
	 * Get value that can be saved in db about rent measurement.
	 * 
	 * @param $valueFromForm value from form
	 */
	private function getFromForm($valueFromForm) {
		switch ($valueFromForm) {
			case 1 :
				$value = Advertisement::RENT_DAILY;
				break;
			case 0 :
				$value = Advertisement::RENT_MONTHLY;
				break;
			default :
				$value = Advertisement::RENT_MONTHLY;
		}
		return $value;
	}
	
	/**
	 * @param $email
	 * @return return generated unique password
	 */
	private function generatePassword($email) {
		$seed = $email . rand ( 0, 10000 ) . $this->_helper->generator->generateCurrentTime ();
		$password = md5 ( $seed );
		$object = $this->advertisement->findAdvertisementByPassword ( $password );
		$numOfTry = 0;
		while ( isset ( $object ) && $numOfTry < self::MAX_TRIES ) {
			$seed = $email . rand ( 0, 10000 ) . $this->_helper->generator->generateCurrentTime ();
			$password = md5 ( $seed );
			$object = $this->advertisement->findAdvertisementByPassword ( $password );
			$numOfTry ++;
		}
		if (isset ( $object )) {
			throw new Exception ( 'Unable to create unique password!' );
		}
		return $password;
	}
	
	/**
	 * List all post action.
	 */
	public function listAction() {
		$config = Zend_Registry::get ( 'config' );
		$this->view->headLink()->prependStylesheet( $config->baseurl . '/css/datePicker.css' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/date.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.datePicker.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.cookie.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/list.js' );
		$this->view->class = "view";
		
		$myNamespace = new Zend_Session_Namespace ( Constant::USER_DATA );
		$city = $this->_getParam ( Constant::VAR_USER_DATA_CITY );
		if (! isset ( $city )) {
			$cityObj = $myNamespace->city;
		} else {
			$cityObj = $this->city->findByName ( $city );
			$myNamespace->city = $cityObj;
		}
		if (! isset ( $cityObj )) {
			throw new Exception ( '暂时不支持所在城市!' );
		}
		// load data from search data session
		$searchData = $myNamespace->searchData;
		if (empty ( $searchData ) || !isset($searchData)) {
			$advertisements = $this->advertisement->findByCity ( $cityObj );
		} else {
			$searchData ['city'] = $cityObj->id;
			$advertisements = $this->advertisement->searchByCriteria ( $searchData );
		}
		
		$this->view->pageTitle = "租个房子 - " . $cityObj->name_cn_long;
		$this->view->currentURI = urlencode($this->_helper->generator->getCurrentURI());
		
		$form = new Search ( $city );
		$this->view->searchForm = $form;
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $_POST )) {
				//$city = $form->getValue(Constant::VAR_USER_DATA_CITY);
				$type = $form->getValue ( Constant::VAR_SEARCH_DATA_TYPE );
				$rent = $form->getValue ( Constant::VAR_SEARCH_DATA_RENT );
				$checkin_date = $form->getValue ( Constant::VAR_SEARCH_DATA_CHECKIN_DATE );
				$checkout_date = $form->getValue ( Constant::VAR_SEARCH_DATA_CHECKOUT_DATE );
				$rent_measurement = $form->getValue ( Constant::VAR_SEARCH_DATA_RENT_MEASUREMENT );
				$sortedBy = $form->getValue ( Constant::VAR_SEARCH_DATA_SORTEDBY );
				$status = $form->getValue ( Constant::VAR_SEARCH_DATA_STATUS );
				$keyword = $form->getValue("keyword");
				$search_business = $form->getValue("search_business");
				$data = array (
					'city' => $cityObj->id, 
					'keyword'=>$keyword, 
					'type' => $type, 
					'rent' => addslashes ( $rent ), 
					'rent_measurement' => $rent_measurement, 
					'checkin_date' => addslashes ( $checkin_date ), 
					'checkout_date' => addslashes ( $checkout_date ),
					'status' => $status, 
//					'sortedBy' => $sortedBy, 
//					'search_business' => $search_business
				);
				// save search option to session
				$myNamespace->searchData = $data;
				$advertisements = $this->advertisement->searchByCriteria ( $data );
			}
		}
		//$this->view->advertisements = $advertisements;
		$this->view->paginator = $this->_helper->paginator ( $this->_getParam ( 'page', 1 ), $advertisements );
	}
	
	/**
	 * View detail post action.
	 */
	public function viewAction() {
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/view.js' );
		
		$this->view->class = "view";
		$city = $this->_getParam ( 'city' );
		$id = $this->_getParam ( 'id' );
		$advertisement = $this->advertisement->findById ( $id );
		if (isset ( $advertisement )) {
			$this->view->viewMode = 'detail';
			$this->view->advertisement = $advertisement;
			$form = new SendIntrestMail ( $advertisement, $city );
			$this->view->form = $form;
			
			$cityObj = $this->city->findByName ( $city );
			
			$this->view->pageTitle = $advertisement->title . "| 租个房子 - " . $cityObj->name_cn_long;
			$pageDesc = "";
			if ($advertisement->type == "want") {
				$this->view->pageImage = "want";
				$pageDesc = $pageDesc . "最高房租:" . $advertisement->rent . $this->_helper->RentFormat ( $advertisement ) . " | ";
				$pageDesc = $pageDesc . "最小面积:" . $advertisement->area . "平方米  | ";
				$pageDesc = $pageDesc . "时间:" . $advertisement->start_date;
				if ($this->_helper->DateIsValid ( $advertisement->stop_date ))
					$pageDesc = $pageDesc . "到" . $advertisement->stop_date;
				else
					$pageDesc = $pageDesc . "起长期";
				$pageDesc = $pageDesc . "\n描述:" . $advertisement->description;
			} else {
				$this->view->pageImage = "lease";
				$pageDesc = $pageDesc . "地址:" . $advertisement->address . " | ";
				$pageDesc = $pageDesc . "房租: " . $advertisement->rent . $this->_helper->RentFormat ( $advertisement ) . " | ";
				$pageDesc = $pageDesc . "时间:" . $advertisement->start_date;
				if ($this->_helper->DateIsValid ( $advertisement->stop_date ))
					$pageDesc = $pageDesc . "到" . $advertisement->stop_date;
				else
					$pageDesc = $pageDesc . "起长期";
				$pageDesc = $pageDesc . "\n描述:" . $advertisement->description;
			
			}
			if ($advertisement->category_id == Category::SHOP){
				$this->view->pageImage = "business";
			}
			
			$this->view->pageDesc = $pageDesc;
			
			if ($this->getRequest ()->isPost ()) {
				if ($form->isValid ( $_POST )) {
//					$isValid = $this->_helper->common->validReCaptcha ($this->_getAllParams());
//					if ($isValid) {
						$id = $form->getValue ( 'id' );
						$city = $form->getValue ( 'city' );
						// sender name
						$name = $form->getValue ( 'name' );
						// sender email
						$email = $form->getValue ( 'email' );
						// body
						$body = $form->getValue ( 'body' );
						$advertisement = $this->advertisement->findById ( $id );
						$user = Advertisement::getUser ( $id );
						//$this->_helper->swiftEmail->sendEmail($body, Advertisement::getUser($advertisement->id)->email, $email, '租个房子');
						$db = Zend_Registry::get ( 'db' );
						$db->beginTransaction ();
						$data = array ('name' => trim ( $name ), 'email' => trim ( $email ) );
						$sendUser = $this->user->addUser ( $data );
						$message = MailTemplate::getEmailMessage ( $name, $email, $user->email, $body, $advertisement, MailTemplate::SENDER_MAIL_BODY );
						$subject = MailTemplate::getEmailSubject ( $advertisement->title, MailTemplate::SUBJECT_SEND_TO_OWNER );
						$mailId = $this->mailQueue->addToQueue ( MailType::USER, $name, $subject, $email, $user->email, $message, $this->_helper->generator->generateCurrentTime () );
						// add advertisement response
						$this->advertisementResponse->addAdvertisementResponse ( $id, $sendUser->id, $mailId );
						
						$message = MailTemplate::getEmailMessage ( $name, $email, $user->email, $body, $advertisement, MailTemplate::SENDER_RECIEPT_BODY );
						$subject = MailTemplate::getEmailSubject ( $advertisement->title, MailTemplate::SUBJECT_SENDER_RECEIPT );
						$this->mailQueue->addToQueue ( MailType::USER, $name, $subject, Constant::SYSTEM_MAIL, $email, $message, $this->_helper->generator->generateCurrentTime () );
						$this->_flashMessenger->addMessage("您的邮件已成功发给贴主了！请等待回音！因为房源紧张，所以可能不会收到贴主的回复。请多找几处。最好电话联系！谢谢您的支持！！");
						$db->commit ();
						$this->_redirect ( '/' . $city . '/bulletin/view/' . $advertisement->id );
//					} else {
////						throw new Exception ( '验证码错误!' );
//					}
				}
			}
		
		} else {
			throw new Exception ( '帖子ID缺失或者帖子未找到!' );
		}
	
	}
	
	/**
	 * Close post action.
	 */
	public function closeAction() {
		$id = $this->_getParam ( 'id' );
		$advertisement = $this->advertisement->findById ( $id );
	
	}
	
	/**
	 * Post admin action.
	 */
	public function adminAction() {
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/create.js' );
		
		$id = $this->_getParam ( 'id' );
		$city = $this->_getParam ( 'city' );
		$advertisement = $this->advertisement->findById ( $id );
		if (empty ( $advertisement )) {
			throw new Exception ( 'ID 缺失或者帖子没找到!' );
		} else {
			$this->view->view_id = $id;
			$form = new AdminPost ( $advertisement, $city );
			$this->view->form = $form;
			if ($this->getRequest ()->isPost ()) {
				if ($form->isValid ( $_POST )) {
//					$isValid = $this->_helper->common->validReCaptcha ( $this->_getAllParams() );
//					if ($isValid) {
						$city = $form->getValue ( 'city' );
						$action = $form->getValue ( 'action' );
						$password = $form->getValue ( 'password' );
						$cityObj = $this->city->findByName ( $city );
						if (! isset ( $cityObj )) {
							throw new Exception ( '暂时不支持所在城市!' );
						}
						switch ($action) {
							case 0 :
								if ($password == $advertisement->password) {
									$db = Zend_Registry::get ( 'db' );
									$db->beginTransaction ();
									$this->advertisement->closeAdvertisement ( $id );
									$db->commit ();
									$this->_redirect ( '/' . $city . '/bulletin/list' );
								} else {
									throw new Exception ( '没有权限关闭该帖子!' );
								}
								break;
							case 1 :
								if ($password == $advertisement->password) {
									$form = new CreateOrUpdatePost ( $advertisement, $password, $cityObj );
									$this->view->form = $form;
								} else {
									throw new Exception ( '没有权限修改该帖子!' );
								}
								break;
							case 2 :
								if ($password == '12345678') {
									$db = Zend_Registry::get ( 'db' );
									$db->beginTransaction ();
									$this->advertisement->deleteAdvertisement ( $id );
									$db->commit ();
									$this->_redirect ( '/' . $city . '/bulletin/list' );
								} else {
									throw new Exception ( '没有权限删除该帖子!' );
								}
								break;
						}
					}
				}
//			}
		}
	}
	
	public function createsessionAction() {
		$aNamespace = new Zend_Session_Namespace ( Constant::USER_DATA );
		$aNamespace->city = $this->_getParam ( 'city' );
		exit ();
	}
	
	public function localhelpAction() {
		$city = $this->_getParam ( 'city' );
		$cityObj = $this->city->findByName ( $city );
		if (isset ( $cityObj )) {
			$article = $this->localHelp->findByCity ( $cityObj );
			$this->view->article = $article;
			$this->view->class = "localhelp";
			
			$this->view->pageTitle = $cityObj->name_cn_long . $cityObj->name_long . " 找房(租房)贴士  | 租个房子 - " . $cityObj->name_cn;
			$this->view->pageDesc = $cityObj->name_cn_long . $cityObj->name_long . " 找房(租房)贴士 ";
		} else {
			throw new Exception ( '暂时不支持所在城市!' );
		}
	
	}
	
	public function createadvertisingagencyAction() {
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/agency.js' );
		$city = $this->_getParam ( 'city' );
		$next = $this->_getParam('next');
		$cityObj = $this->city->findByName ( $city );
		if (isset ( $cityObj )) {
			$form = new CreateAdvertisingAgency($cityObj, $next);
			$this->view->form = $form;
			
			if ($this->getRequest ()->isPost ()) {
				$formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
//					$isValid = $this->_helper->common->validReCaptcha($this->_getAllParams());
//                	if ($isValid) {
						$city_id = $this->_getParam ( AdvertisingAgency::CITY );
						$name = $this->_getParam ( User::NAME );
						$email = $this->_getParam ( User::EMAIL );
						$mobile = $this->_getParam ( User::MOBILE );
						$description = $this->_getParam(AdvertisingAgency::DESCRIPTION);
						$comment = $this->_getParam(AdvertisingAgency::COMMENT);
						
						$db = Zend_Registry::get("db");
						$db->beginTransaction();
						$data = array(
							User::NAME => $name,
							User::EMAIL => $email,
							User::MOBILE => $mobile
						);
						$user = $this->user->addUser($data);
						$data = array(
							AdvertisingAgency::CITY => $city_id,
							AdvertisingAgency::USER => $user->id,
							AdvertisingAgency::DESCRIPTION => $description,
							AdvertisingAgency::COMMENT => $comment,
							AdvertisingAgency::CREATED => $this->_helper->generator->generateCurrentTime(),
							AdvertisingAgency::MODIFIED => $this->_helper->generator->generateCurrentTime(),
							AdvertisingAgency::STATUS => PostStatus::ACTIVE
						);
						$advertisingAgency = $this->advertisingAgency->addEntry($data);
						
						// send out emails
						$message = MailTemplate::getAdvertisingAgencyEmailNotificationMessage($user, $advertisingAgency, MailTemplate::ADVERTISING_AGENCY_REQUEST_BODY);
						$this->mailQueue->addToQueue ( MailType::SYSINFO, null, MailTemplate::SUBJECT_ADVERTISING_AGENCY_REQUEST, Constant::SYSTEM_MAIL, Constant::ADVERTISING_AGENCY_RECIPIENTS, $message, $this->_helper->generator->generateCurrentTime () );
						$message = MailTemplate::getAdvertisingAgencyEmailNotificationMessage($user, $advertisingAgency, MailTemplate::ADVERTISING_AGENCY_REQUEST_RECEIPT_BODY);
						$this->mailQueue->addToQueue ( MailType::SYSINFO, null, MailTemplate::SUBJECT_ADVERTISING_AGENCY_REQUEST_RECEIPT, Constant::SYSTEM_MAIL, $user->email, $message, $this->_helper->generator->generateCurrentTime () );
						$db->commit();
						
						
        				$this->_flashMessenger->addMessage("您的需求已经发送，我们会尽快与您取得联系！谢谢！");
						
						if (empty($next)) {
							$this->_redirect("/".$cityObj->name."/bulletin/list");
						} else {
							$this->_redirect(urldecode($next));
						}
//					} else {
//						echo "Capthca is not correct!";
//					}
				} else {echo "invalid form";}
			}
		} else {
			throw new Exception ( '暂时不支持所在城市!' );
		}
	}
	
	/**
	 * Go through all bulletin that contain given keywords, if so, it will be marked as 'SPAM'
	 */
	public function checkspambulletinAction() {
		$this->_helper->viewRenderer->setNoRender();   //view info disabled
		$this->_helper->layout->disableLayout();
		
//		$lines = file($config->advertisement->spam->keywords);
		$keywords = $this->readKeywordFile();
//		foreach($lines as $line_num => $line) {
//			$keywords[$line_num] = str_replace(array("\r", "\n"), '', $line);
//		}
//		$data = file_get_contents($config->advertisement->spam->keywords);   
//		$keywords = explode(PHP_EOL, $data);
		$db = Zend_Registry::get("db");
		$db->beginTransaction();
		foreach ($keywords as $keyword) {
			if (!empty($keyword) && strlen($keyword) >=5 ) {
			$numOfRows = $this->advertisement->updateSpamBulletins($keyword);
				echo $numOfRows." has affected!<br/>";
			} else {
				echo "keyword is null or less than 5 characters, nothing to do!<br/>";
			}
		}
		$db->commit();
		exit;
	}
	
	private function readKeywordFile() {
		$keywords = array();
		$config = Zend_Registry::get("config");
		// Ooen the file
		$fh = fopen($config->advertisement->spam->keywords, "r");
		$index = 0;
		while ($line = fgets($fh, 40)) {
		  $keywords[$index++] = trim($line);
		}
		fclose($fh);
		
		return $keywords;
	}
	
	public function testAction() {
	/*
		$form = new ReCaptcha();
		$this->view->form = $form;
		if ($this->_request->isPost()) {
			$config = Zend_Registry::get("config");
		$publickey = $config->recaptcha->public->key;
		$privatekey = $config->recaptcha->private->key;
                $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
			$result = $recaptcha->verify($this->_getParam('recaptcha_challenge_field'),
                                             $this->_getParam('recaptcha_response_field'));
			print_r($result);
			if ($result->isValid()){
			echo "right";
		} else {
			echo "wrong";
		}
											 
		}*/
		
		$form = new ReCaptcha();
		$this->view->form = $form;
        if ($this->_request->isPost()) {
			//if ($form->isValidPartial ( $_POST )) {
				$config = Zend_Registry::get("config");
		$publickey = $config->recaptcha->public->key;
		$privatekey = $config->recaptcha->private->key;
		$recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
		$result = $recaptcha->verify($this->_getParam('recaptcha_challenge_field'), $this->_getParam('recaptcha_response_field'));
		print_r($result);
			
            	$isValid = $this->_helper->common->validReCaptcha($this->_getAllParams());
            	if (!$isValid) {
                	echo "adfa";
                    //ReCaptcha validation error
                    //Your action here...
               } else {
               	echo "right";
               }
            //}
        }
        
	}
}

