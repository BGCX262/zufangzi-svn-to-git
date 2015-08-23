<?php
class ReCaptcha extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        //Add your elements here...
        $config = Zend_Registry::get("config");
		$publickey = $config->recaptcha->public->key;
		$privatekey = $config->recaptcha->private->key;
        $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);

        //Translate in your language
        $recaptcha_it_translation =
            array('visual_challenge' => "Verifica video",
                  'audio_challenge' => "Verifica audio",
                  'refresh_btn' => "Effettua una nuova verifica",
                  'instructions_visual' => "Scrivi le due parole",
                  'instructions_audio' => "Scrivi quello che ascolti",
                  'help_btn' => "Aiuto",
                  'play_again' => "Riascolto di nuovo l'audio",
                  'cant_hear_this' => "Scarica l'audio come MP3",
                  'incorrect_try_again' => "Incorretto. Prova ancora.");

        $recaptcha->setOption('custom_translations', $recaptcha_it_translation);
        //Change theme
        $recaptcha->setOption('theme', 'clean');

        $captcha = new Zend_Form_Element_Captcha('challenge',
              array('captcha'        => 'ReCaptcha',
                    'captchaOptions' => array('captcha' => 'ReCaptcha',
                                             'service' => $recaptcha),
              		'ignore' => true
              ));

        $this->addElement($captcha);

        // Add the submit button
        $this->addElement('submit', 'submit', array('label' => 'Submit'));
    }
}
?>
