<?php
class Zend_Controller_Action_Helper_Common extends Zend_Controller_Action_Helper_Abstract {
	
	public function validateCaptcha($captcha) {
		$captchaId = $captcha ['id'];
		// And here's the user submitted word...  
		$captchaInput = $captcha ['input'];
		// We are accessing the session with the corresponding namespace  
		// Try overwriting this, hah!  
		$captchaSession = new Zend_Session_Namespace ( 'Zend_Form_Captcha_' . $captchaId );
		// To access what's inside the session, we need the Iterator  
		// So we get one...  
		$captchaIterator = $captchaSession->getIterator ();
		// And here's the correct word which is on the image...  
		$captchaWord = $captchaIterator ['word'];
		// Now just compare them...  
		if ($captchaInput == $captchaWord) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Check if user has filled in correct recaptcha word.
	 * 
	 * @param $requestParams incoming request parameters
	 * @return true if valid, otherwise, return false
	 */
	public function validReCaptcha($requestParams) {
		if (empty($requestParams['recaptcha_response_field'])) {
			return false;
		} else {
			$config = Zend_Registry::get("config");
			$publickey = $config->recaptcha->public->key;
			$privatekey = $config->recaptcha->private->key;
			$recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
			$result = $recaptcha->verify($requestParams['recaptcha_challenge_field'], $requestParams['recaptcha_response_field']);
			return $result->isValid();
		}
	}
}

?>