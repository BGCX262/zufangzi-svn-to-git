<?php
class SendIntrestMail extends Zend_Form {
	
	private $advertisement;
	private $city;
	
	public function SendIntrestMail($advertisement, $city) {
		$this->advertisement = $advertisement;
		$this->city = $city;
		$this->__construct();
	}
	
	public function init() {
		$this->setMethod('POST');
		$this->setName('sendEmailForm');
		//$this->setAction('index/view');
		
		$element = new Zend_Form_Element_Hidden('id');
		$element->setValue($this->advertisement->id);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Hidden('city');
		$element->setValue($this->city);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('name');
		$element->setLabel('怎么称呼您');
		$element->setRequired(true);
		$element->setDescription('必填');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('email');
		$element->setLabel('您的Email');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_EmailAddress());		
		$element->addFilter(new Zend_Filter_HtmlEntities());
		$element->addFilter(new Zend_Filter_StripTags());
		$element->addValidator('NotEmpty');
		$element->setDescription('必填');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Textarea('body');
		$element->setLabel('内容');
		$element->setRequired(true);
		$element->addValidator('NotEmpty');
		$element->setDescription('必填');
		$element->setAttrib('rows',4);
		$this->addElement($element);
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$element = new Zend_Form_Element_Submit('send');
		$element->setValue('发送');
		$element->removeDecorator('Label');
		$this->addElement($element);
	}
}
?>