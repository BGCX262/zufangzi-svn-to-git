<?php

class GuestBook extends Zend_Form {
	
	public function init() {
		$this->setMethod('POST');
		$this->setName('guestForm');
		
		$element = new Zend_Form_Element_Text('name');
		$element->setLabel('怎么称呼您');
		$element->setDescription('必填，中英文都可，2到30个字');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('email');
		$element->setLabel('您的Email');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_EmailAddress());	
		$element->addValidator('NotEmpty');
		$element->setDescription('必填');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Textarea('body');
		$element->setLabel('您的留言');
		$element->setAttrib('rows',4);
		$element->addValidator('NotEmpty');
		$this->addElement($element);
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$element = new Zend_Form_Element_Submit('post');
		$element->setValue('提交')->removeDecorator('Label');
		$this->addElement($element);
		
	}	
}
?>