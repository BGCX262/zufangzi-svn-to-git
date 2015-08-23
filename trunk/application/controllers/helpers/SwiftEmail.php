<?php
require_once 'Swift/lib/swift_required.php';

class Zend_Controller_Action_Helper_SwiftEmail extends Zend_Controller_Action_Helper_Abstract {
	
	/**
	 * Send email using swift api.
	 * 
	 * @param $body
	 * @param $recipients
	 * @param $from
	 * @param $subject
	 */
	public function sendEmail($body, $recipients, $from, $subject) {
		$config = Zend_Registry::get('config');
		$failures = '';
		Swift_Preferences::getInstance()->setCharset('UTF-8');
		//Create the Transport
		$transport = Swift_SmtpTransport::newInstance ($config->mail->server->name, $config->mail->server->port, $config->mail->server->security)
		->setUsername($config->mail->username)
		->setPassword($config->mail->password);
		$mailer = Swift_Mailer::newInstance ($transport);
		
		$message = Swift_Message::newInstance ();
		$headers = $message->getHeaders();
		$headers->addTextHeader("signed-by", Constant::EMAIL_DOMAIN);
		//Give the message a subject
		$message->setSubject($subject);
		//Set the From address with an associative array
		$message->setFrom($from);
		//Set the To addresses with an associative array
		$message->setTo($recipients);
		//Give it a body
		$message->setBody($body);
		$message->setContentType("text/html");
		//Send the message
		$myresult = $mailer->batchSend($message);
		if ($myresult) {
		  return null;
		} else {
		  return Constant::EMAIL_FAIL_MESSAGE;
		}
	}
}