<?php

class IndexController extends Zend_Controller_Action {
	private $mailQueue;
	private $user;
	private $message;
	
	public function init() {
		$this->mailQueue = new MailQueue ( );
		$this->user = new User();
		$this->message = new Message();
		$this->view->pageLogo="big";
		
		$this->initView();
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');		 
		$this->view->flashmsgs = $this->_flashMessenger->getMessages();
	}
	
	public function indexAction() {
		$this->_helper->getHelper ( 'layout' )->disableLayout (); //template disabled
		$myNamespace = new Zend_Session_Namespace ( Constant::USER_DATA );
		$myNamespace->searchData = null;
	//$this->_redirect('/stockholm/bulletin/list');
	}
	
	public function testAction() {
	}
	
	
	public function contactAction() {
		
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/contact.js' );
		$form = new ContactUs ( );
		
		$this->view->form = $form;
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $_POST )) {
//				$isValid = $this->_helper->common->validReCaptcha($this->_getAllParams());
//				
//				if ($isValid) {
					$name = $form->getValue ( 'name' );
					$email = $form->getValue ( 'email' );
					$body = nl2br ( $form->getValue ( 'body' ) );
					
					$db = Zend_Registry::get ( 'db' );
					$db->beginTransaction ();
					$this->mailQueue->addToQueue ( MailType::CONTACT_US, $name, '联系我们', $email, Constant::SYSTEM_MAIL, $body, $this->_helper->generator->generateCurrentTime () );
					$db->commit ();
					$this->_flashMessenger->addMessage("您的留言已经发送，我们会尽快处理并与您取得联系！谢谢您对我们的支持！！");
					$this->_redirect ( '/contact' );
//				} else {
//					throw new Exception('验证码错误!');
//				}
			}
		}
	}
	
public function newcityAction() {
		
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/contact.js' );
		$form = new AddNewCity ( );
		
		$this->view->form = $form;
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValidPartial ( $_POST )) {
				$isValid = $this->_helper->common->validReCapthca($this->_getAllParams());
				
				if ($isValid) {
					$name = $form->getValue ( 'name' );
					$email = $form->getValue ( 'email' );
					$body = nl2br ( $form->getValue ( 'body' ) );
					
					$db = Zend_Registry::get ( 'db' );
					$db->beginTransaction ();
					$this->mailQueue->addToQueue ( MailType::CONTACT_US, $name, '新开城市', $email, Constant::SYSTEM_MAIL, $body, $this->_helper->generator->generateCurrentTime () );
					$db->commit ();
					$this->_flashMessenger->addMessage("您的留言已经发送，我们会尽快处理并与您取得联系！谢谢您对我们的支持！！");
					$this->_redirect ( '/newcity' );
				} else {
					throw new Exception('验证码错误!');
				}
			}
		}
	}
	
	public function aboutAction() {
	}
	public function helpAction() {
	}
	public function linksAction() {
	}
	
	/**
	 * Guest book action.
	 */
	public function guestbookAction() {
	    /*
		$config = Zend_Registry::get ( 'config' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/jquery.validate.min.js' );
		$this->view->headScript ()->appendFile ( $config->baseurl . '/js/guestbook.js' );
		$this->view->class="guestbook";
		$form = new GuestBook();
		$this->view->form = $form;
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $_POST )) {
//				$isValid = $this->_helper->common->validReCaptcha($this->_getAllParams());
//				
//				if ($isValid) {
					$name = $form->getValue('name');
					$email = $form->getValue('email');
					$body = nl2br($form->getValue('body'));
					$db = Zend_Registry::get('db');
					$db->beginTransaction();
					$data = array(
						'name' => trim($name),
						'email' => trim($email),
						'mobile' => null
					);
					$user = $this->user->addUser($data);
					$data = array(
						'user_id' => $user->id,
						'type' => MessageType::GUEST_BOOK,
						'body' => $body,
						'status' => MessageStatus::ACTIVE,
						'created' => $this->_helper->generator->generateCurrentTime(),
						'ipaddress' => $this->_helper->generator->getIpAddress()
					);
					$this->message->postMessage($data);
					$this->_flashMessenger->addMessage("您的留言添加！谢谢您对我们的支持！！");
					$db->commit();
//				} else {
//					throw new Exception('验证码错误!');
//				}
			}
		}
		$messages = $this->message->findAllActive();
		$this->view->paginator = $this->_helper->paginator($this->_getParam('page', 1), $messages);
		*/
	    exit;
	}
}