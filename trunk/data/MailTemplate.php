<?php

class MailTemplate {
	const TAG_SENDER_NAME = '#SENDER_NAME';
	const TAG_SENDER_EMAIL = '#SENDER_EMAIL';
	const TAG_SENDER_MOBILE = '#SENDER_MOBILE';
	const TAG_BODY = '#BODY';
	const TAG_COMMENT = '#COMMENT';
	const TAG_RECIPIENT = '#RECIPIENT';
	const TAG_POST_TITLE = '#POST_TITLE';
	const TAG_PASSWORD = '#PASSWORD';
	const TAG_POST_URL = '#POST_URL';
	const TAG_ADMIN_POST_URL = '#ADMIN_POST_URL';
	const TAG_DATE = '#DATE';
	const SITE_EMAIL = 'no-reply@weimingge.com';
	
	const SUBJECT_SEND_TO_OWNER = '关于帖子 #POST_TITLE';
	const SUBJECT_SENDER_RECEIPT = '关于帖子 #POST_TITLE';
	const SUBJECT_CREATE_POST = '您在 zugefangzi.com 发了一个帖子';
	const SUBJECT_CLOSE_NOTICATION = 'Close advertisement notification';
	const SUBJECT_CLOSED_POST = 'We close your advertisement';
	const SUBJECT_ADVERTISING_AGENCY_REQUEST = 'Incoming advertising agency request';
	const SUBJECT_ADVERTISING_AGENCY_REQUEST_RECEIPT = 'We have received your advertising agency request';
	
	const CREATED_POST_MAIL_BODY = '您发了一个新帖子 #POST_TITLE<br /><br />您的密码是 #PASSWORD<br /><br />点击这里<a href="#POST_URL">查看/修改</a>您的帖子 <br/><br/>-------------------------------------------------------- <br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const SENDER_MAIL_BODY = '-- #SENDER_NAME(#SENDER_EMAIL) 给您发了封信 -- <br/><br/><p>#BODY</p>--------------------------------------------------------<br/><br/>帖子链接 <a href="#POST_URL" target="_blank">点击这里</a><br/>--------------------------------------------------------<br/>请不要直接回复本邮件，请发送给邮件给 #SENDER_NAME(#SENDER_EMAIL) <br/><br/>-------------------------------------------------------- <br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const SENDER_RECIEPT_BODY = '-- 您给 #RECIPIENT 发了封信 -- <br/><br/><p>#BODY</p>帖子链接 <a href="#POST_URL" target="_blank">点击这里</a><br/>-------------------------------------------------------- <br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const CLOSE_NOTIFICATION_BODY = '您好， #RECIPIENT! <br/>您的帖子<strong>#POST_TITLE</strong>已经发布超过2周了， 如果您的房子已经租出或者已经找到您想找的房子，请关闭您的帖子，点击<a href="#ADMIN_POST_URL">这里</a>关闭您的帖子<br>您的密码是<strong>#PASSWORD</strong><p><br/><br/>帖子链接 <a href="#POST_URL" target="_blank">点击这里</a><br/>--------------------------------------------------------<br/><br/>Hi, #RECIPIENT! <br/>14 days has past, if you have already found the tenant, to avoid receive mails or calls from room seeker, please close down your advertisement by clicking <a href="#ADMIN_POST_URL">here</a><br/>Your password is <strong>#PASSWORD</strong><br/><br/>Post link <a href="#POST_URL" target="_blank">Click here</a><br/>-------------------------------------------------------- <br/><br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const CLOED_POST_BODY = '您好，#RECIPIENT! <br/>我们关闭了您的帖子<strong>#POST_TITLE</strong>，因为您帖子的结束日期已经失效。如果您想查看帖子， 请点击<a href="#POST_URL" target="_blank">这里</a>。<br/>--------------------------------------------------------<br/><br/>Hi, #RECIPIENT! <br/>We closed your advertisement (<strong>#POST_TITLE</strong>) because your advertisement has been expired (deadline of your post has been passed). If you want to view your post, please click <a href="#POST_URL" target="_blank">here</a>.<br/>-------------------------------------------------------- <br/><br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const ADVERTISING_AGENCY_REQUEST_BODY = '有一个新的中介求房帖子<br/><br/>=============================<br/>Name: #SENDER_NAME<br/>Email: #SENDER_EMAIL<br/>Mobile: #SENDER_MOBILE<br/>Description: #BODY<br/>Comment: #COMMENT<br/>Date: #DATE<br/>=============================<br/><br/><br/><br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	const ADVERTISING_AGENCY_REQUEST_RECEIPT_BODY = '我们收到了您的求房帖子， 如果有任何符合条件的房源，我们会尽快恢复您<br/><br/>=============================<br/>帖子详情<br/>Name: #SENDER_NAME<br/>Email: #SENDER_EMAIL<br/>Mobile: #SENDER_MOBILE<br/>Description: #BODY<br/>Comment: #COMMENT<br/>Date: #DATE<br/>=============================<br/><br/><br/><br/>http://www.zugefangzi.com   租个房子 - 海外华人租房网';
	
	/**
	 * 
	 * @param $user
	 * @param $advertisingAgency
	 * @return message
	 */
	public function getAdvertisingAgencyEmailNotificationMessage($user, $advertisingAgency, $messageTemplate) {
		$message = '';
		$message = str_replace(self::TAG_SENDER_EMAIL, $user->email, $messageTemplate);
		$message = str_replace(self::TAG_SENDER_NAME, empty($user->name) ? "":$user->name, $message);
		$message = str_replace(self::TAG_SENDER_MOBILE, empty($user->mobile) ? "":$user->mobile, $message);
		$message = str_replace(self::TAG_BODY, $advertisingAgency->description, $message);
		$message = str_replace(self::TAG_COMMENT, $advertisingAgency->comment, $message);
		$message = str_replace(self::TAG_DATE, $advertisingAgency->created, $message);
		$message = nl2br($message);
		return $message;
	}
	
	/**
	 * 
	 * @param $senderName
	 * @param $sender
	 * @param $recipient
	 * @param $body
	 * @param $advertisement
	 * @param $messageTemplate
	 */
	public static function getEmailMessage($senderName, $sender, $recipient, $body, $advertisement=null, $messageTemplate) {
		$message = '';
		$message = str_replace(self::TAG_SENDER_EMAIL, $sender, $messageTemplate);
		$message = str_replace(self::TAG_BODY, $body, $message);
		$message = str_replace(self::TAG_RECIPIENT, $recipient, $message);
		$message = str_replace(self::TAG_SENDER_NAME, $senderName, $message);
		$city = Advertisement::getCity($advertisement->id)->name;
		
		$config = Zend_Registry::get('config');
		$url = $config->baseurl.$city.'/bulletin/view/'.$advertisement->id;
		$message = str_replace(self::TAG_POST_URL, $url, $message);
		$message = nl2br($message);
		return $message;
	}
	
	/**
	 * Replace subject with given token.
	 * 
	 * @param $postTitle
	 * @param $subjectTemplate
	 */
	public static function getEmailSubject($postTitle, $subjectTemplate) {
		$message = '';
		$message = str_replace(self::TAG_POST_TITLE, $postTitle, $subjectTemplate);
		return $message;
	}
	
	/**
	 * Return creation message.
	 * 
	 * @param $advertisement
	 */
	public static function getCreationEmailMessage($advertisement) {
		$message = '';
		$message = str_replace(self::TAG_POST_TITLE, $advertisement->title, self::CREATED_POST_MAIL_BODY);
		$message = str_replace(self::TAG_PASSWORD, $advertisement->password, $message);
		$config = Zend_Registry::get('config');
		$city = Advertisement::getCity($advertisement->id)->name;
		$url = $config->baseurl.$city.'/bulletin/view/'.$advertisement->id;
		$message = str_replace(self::TAG_POST_URL, $url, $message);
		return $message;
	}
	
/**
     * Return notification message.
     * 
     * @param $advertisement
     * @param $recipient
     * @param $messageTemplate
     * @param $type
     */
    public static function getNotificationEmailMessage($advertisement, $recipient, $messageTemplate, $type) {
        $message = '';
        $message = str_replace(self::TAG_RECIPIENT, $recipient, $messageTemplate);
        $message = str_replace(self::TAG_PASSWORD, $advertisement->password, $message);
        $message = str_replace(self::TAG_POST_TITLE, $advertisement->title, $message);
        $config = Zend_Registry::get('config');
        $city = Advertisement::getCity($advertisement->id)->name;
        $url = $config->baseurl.$city.'/bulletin/admin/'.$advertisement->id;
        $message = str_replace(self::TAG_ADMIN_POST_URL, $url, $message);
        $url = $config->baseurl.$city.'/bulletin/view/'.$advertisement->id;
        $message = str_replace(self::TAG_POST_URL, $url, $message);
        $message = nl2br($message);
        return $message;
    }
    
}
?>