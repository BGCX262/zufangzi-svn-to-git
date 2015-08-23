<?php
require_once("Elements.php");

class AddNewCity extends Zend_Form {
	
	public function init() {
		$languageFile = Zend_Registry::get ( 'languageFile' );
		$translate = new Zend_Translate ( 'array', $languageFile, 'zh_CN' );
		$this->setTranslator ( $translate );
		
		$this->setMethod ( 'POST' );
		$this->setName ( 'contactForm' );
		
		$element = new Zend_Form_Element_Text ( 'name' );
		$element->setLabel ( '怎么称呼您' );
		$this->addElement ( $element );
		
		$element = new Zend_Form_Element_Text ( 'email' );
		$element->setLabel ( '您的Email' );
		//$element->setRequired(true);
		$this->addElement ( $element );
		
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$this->addDisplayGroup ( array ('name', 'email','captcha' ), 'leftSection' );
		$this->getDisplayGroup ( 'leftSection' )->removeDecorator ( 'DtDdWrapper' );
		
		$element = new Zend_Form_Element_Textarea ( 'body' );
		$element->setLabel ( '想要开通城市地区和找房贴士，关于您的简单介绍' );
		$element->addPrefixPath ( 'My_Validator', 'My/Validator/', 'validate' );
		$element->addValidator ( 'FormValueNotNull', true );
		//$element->setRequired(true);
		$element->setAttrib ( 'rows', 13 );
		$this->addElement ( $element );
		
		$this->addDisplayGroup ( array ('body' ), 'rightSection' );
		$this->getDisplayGroup ( 'rightSection' )->removeDecorator ( 'DtDdWrapper' );
		
		
		$element = new Zend_Form_Element_Submit ( 'post' );
		$element->removeDecorator ( 'Label' );
		$this->addElement ( $element );
	}
}
?>
