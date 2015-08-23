<?php
class Elements {
	
	/**
	 * Add captcha element to the given form.
	 * 
	 * @param $form
	 */
	public function addCaptcha($form) {
		$config = Zend_Registry::get('config');
		$form->addElement(
			'Captcha', 'captcha', 
			array (
			'label' => '能看清下面的是什么吗',
			'required' => true, 
			'captcha' => array (
				'captcha' => 'image', 
				'name' => 'foo', 
				'wordLen' => 6, 
				'font' => $config->captcha->font->dir, 
				'fontSize' => 22, 
				'imgDir' => $config->captcha->img->dir, 
				'imgUrl' => $config->captcha->img->url, 
				'timeout' => 300,
				'dotNoiseLevel' => 10,
				'lineNoiseLevel' => 5,
				'gcFreq' => 10) ) 
		);
	}
	
	public function addReCaptcha($form) {
		$config = Zend_Registry::get("config");
		$publickey = $config->recaptcha->public->key;
		$privatekey = $config->recaptcha->private->key;
        $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);

        //Translate in your language
        $recaptcha_cn_translation =
            array('visual_challenge' => "图片验证",
                  'audio_challenge' => "音频验证",
                  'refresh_btn' => "看不清，换一张",
                  'instructions_visual' => "图片验证说明",
                  'instructions_audio' => "音频验证说明",
                  'help_btn' => "帮助",
                  'play_again' => "重放",
                  'cant_hear_this' => "听不到? 点这里",
                  'incorrect_try_again' => "验证码错误!");

        $recaptcha->setOption('custom_translations', $recaptcha_cn_translation);
        //Change theme
        $recaptcha->setOption('theme', 'clean');

        $captcha = new Zend_Form_Element_Captcha('challenge',
              array('captcha'        => 'ReCaptcha',
                    'captchaOptions' => array('captcha' => 'ReCaptcha',
                                             'service' => $recaptcha),
              		'ignore' => false,
              ));
        $captcha->setRequired(true);
        $form->addElement($captcha);
	}
}
?>