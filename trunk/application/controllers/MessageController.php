<?php
class MessageController extends Zend_Controller_Action {
	
	private $mailQueue;
	private $advertisement;
	private $advertisementNotification;
	
	public function init() {
		$this->mailQueue =  new MailQueue();
		$this->advertisement = new Advertisement();
		$this->advertisementNotification = new AdvertisementNotification();
	}
	
	/**
	 * Send mails from mail_queue periodically.
	 * First, need to have a security check that only allow 
	 * to run the script when ctl and corresponding crc matches.
	 * Second, collect all emails that need to be sent out,
	 * criteria can be send only emails marked as SENT 'false',
	 * can also given timestamp, by default, it only send the mails
	 * created the same day.
	 * 
	 * Example: ctl 1686825444, crc e233847ad0507ae1d0890633ced692b9
	 */
	public function sendemailAction() {
		$this->_helper->viewRenderer->setNoRender();   //view info disabled
		$this->_helper->layout->disableLayout();
		//$body, $recipients, $from, $subject
		$ctl = $this->_getParam('ctl');
		$crc = $this->_getParam('crc');
		$customerKey = '';
		$startDate = $this->_getParam('start');
		
		$generated_crc = md5($ctl);
		foreach ($this->mailQueue->findUnsentMails() as $mail) {			
			if ($generated_crc == $crc) {
				$from = $mail->sender;
				$failMessage = Constant::EMAIL_FAIL_MESSAGE;
				$db = Zend_Registry::get('db');
				$db->beginTransaction();
				if (!empty($mail->message)) {
				    $failMessage = $this->_helper->swiftEmail->sendEmail($mail->message, $mail->recipient, $mail->sender, $mail->subject);
				}
				$this->mailQueue->updateStatus($mail->id, $failMessage, $this->_helper->generator->generateCurrentTime());
				$db->commit();
			} else {
				throw new Exception('Not allowed to run the service!');
			}
		}
	}
	
	/**
	 * Populate notification list.
	 * 
	 * Example: ctl 1686825444, crc e233847ad0507ae1d0890633ced692b9
	 */
	public function populatenoficationlistAction() {
		$this->_helper->viewRenderer->setNoRender();   //view info disabled
		$this->_helper->layout->disableLayout();
		
		$ctl = $this->_getParam('ctl');
		$crc = $this->_getParam('crc');
		$customerKey = '';
		$startDate = $this->_getParam('start');
		
		$generated_crc = md5($ctl);
		if ($generated_crc == $crc) {
		
		$notifiedBeforeDate = $this->_helper->generator->manipulatDate($this->_helper->generator->generateCurrentTime(), -Constant::NOTIFICATION_DAY, Zend_Date::DAY);
		$advertisements = $this->advertisement->findAdvertisementToSendNofication($notifiedBeforeDate);
		$numOfAdvs = count($advertisements);
		echo "Check advertisments created before ".$notifiedBeforeDate."<br/>";
		echo $numOfAdvs." advertisements found might need to be notified<br/>";
		$numOfAffected = 0;
		foreach ($advertisements as $advertisement) {
			$data = array(
			    "advertisement_id" => $advertisement->id,
			    "type" => "close_advertisement",
			    "status" => "valid",
			    "created" => $this->_helper->generator->generateCurrentTime()
			);
			
			$db = Zend_Registry::get("db");
			$db->beginTransaction();
			// check if notification to be added is already exist.
			// only add to mail queue if it's a new one.
			$notification = $this->advertisementNotification->findByAdvertisementTypeAndStatus($advertisement->id, "close_advertisement", "valid");
			if (empty($notification)) {
				$this->advertisementNotification->addOneEntry($data);
				
				$subject = MailTemplate::SUBJECT_CLOSE_NOTICATION;
				$user = Advertisement::getUser($advertisement->id);
				$pos = strpos($user->email, "@zugefangzi.com");
				if ($pos == false) {
					$sender = Constant::SYSTEM_MAIL;
				} else {
					$sender = Constant::EMAIL_TO_SERVER;
				}
				$recipient = $user->email;
				$message = MailTemplate::getNotificationEmailMessage($advertisement, $recipient, MailTemplate::CLOSE_NOTIFICATION_BODY, Constant::CLOSE_NOTIFICATION);
				$this->mailQueue->addToQueue(MailType::CLOSE_NOTIFICATION, null, $subject, $sender, $recipient, $message, $this->_helper->generator->generateCurrentTime());
				$numOfAffected++;
			}
			$db->commit();
		}
		echo $numOfAffected." advertisement(s) are affected";
		echo "<hr/>";
		// close down expired advertisements
		$advertisements = $this->advertisement->findAdvertisementToClose();
		$numOfAdvs = count($advertisements);
		echo "Close expired advertisements<br/>";
		echo $numOfAdvs." advertisements found<br/>";
		$numOfAffected = 0;
		$db = Zend_Registry::get("db");
		$db->beginTransaction();
		foreach($advertisements as $advertisement) {
			$advertisement->status = PostStatus::CLOSED;
			$advertisement->save();
			
			$subject = MailTemplate::SUBJECT_CLOSED_POST;
			$user = Advertisement::getUser($advertisement->id);
			$pos = strpos($user->email, "@zugefangzi.com");
			if ($pos == false) {
				$sender = Constant::SYSTEM_MAIL;
			} else {
				$sender = Constant::EMAIL_TO_SERVER;
			}
			$recipient = $user->email;
			$message = MailTemplate::getNotificationEmailMessage($advertisement, $recipient, MailTemplate::CLOED_POST_BODY, Constant::CLOSE_NOTIFICATION);
			$this->mailQueue->addToQueue(MailType::SYSINFO, null, $subject, $sender, $recipient, $message, $this->_helper->generator->generateCurrentTime());
			$numOfAffected++;
		}
		$db->commit();
		echo $numOfAffected." advertisement(s) are affected";
		} else {
			throw new Exception('Not allowed to run the service!');
		}
	}
}
?>