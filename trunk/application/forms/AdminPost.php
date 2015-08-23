<?php

class AdminPost extends Zend_Form {
	private $advertisement;
	private $city;
	
	public function AdminPost($advertisement, $city) {
		$this->advertisement = $advertisement;
		$this->city = $city;
		$this->__construct();
	}
	
	public function init() {
		$this->setMethod('POST');
		$this->setName("adminForm");
		$element = new Zend_Form_Element_Hidden('id');
		$element->setValue($this->advertisement->id);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Hidden('city');
		$element->setValue($this->city);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Radio('action');
		$element->setMultiOptions(
			array(
				'0' => '已解决, 关闭帖子',
				'1' => '打错了些东东, 修改帖子',
				'2' => '我是管理员, 删除帖子'
			)
		);
		$element->setValue(0);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('password');
		$element->setLabel('密码');
		$element->setDescription('忘记密码?');
		$this->addElement($element);
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$element = new Zend_Form_Element_Submit('post');
		$element->setLabel('提交')->removeDecorator('Label');
		$this->addElement($element);
		
		
		$this->addDisplayGroup(array('password','captcha','post'),'leftMargin');
		$this->getDisplayGroup('leftMargin')->removeDecorator('DtDdWrapper');
		
	}
}
?>