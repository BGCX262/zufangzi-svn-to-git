<?php
class CreateAdvertisingAgency extends Zend_Form {
	
	private $city;
	private $next;
	
	public function CreateAdvertisingAgency($city, $next=null) {
		$this->city = $city;
		$this->next = $next;
		$this->__construct();
	}
	
	public function init() {
		$this->setMethod("POST");
		$this->setName("createAdAgencyForm");
		
		if (!empty($this->next)) {
			$element = new Zend_Form_Element_Hidden("next");
			$element->setValue($this->next);
			$this->addElement($element);
		}
		
		$element = new Zend_Form_Element_Hidden(AdvertisingAgency::CITY);
		$element->setValue($this->city->id);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text(User::NAME);
		$element->setLabel('怎么称呼您');
		$element->setDescription('必填，中英文都可，2到30个字');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text(User::EMAIL);
		$element->setLabel('您的Email');
		$element->setDescription('必填，方便我们联系您');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_EmailAddress());	
		$element->addValidator('NotEmpty');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text(User::MOBILE);
		$element->setLabel('您的手机号');		
		$element->addFilter(new Zend_Filter_HtmlEntities());
		$element->addFilter(new Zend_Filter_StripTags());
		$element->setDescription('方便我们及时联系您');
		$this->addElement($element);
				
		$this->addDisplayGroup(array('city_id','name', 'email','mobile'),'aboutYou');
		$this->getDisplayGroup('aboutYou')->removeDecorator('DtDdWrapper');
		
		$element = new Zend_Form_Element_Textarea(AdvertisingAgency::DESCRIPTION);
		$element->setAttrib('rows',6);
		$element->setLabel("有哪些人要入住？基本情况？尽量详细！");
		$element->setDescription('如：三口之家，男30岁在爱立信有稳定工作，不吸烟；女27岁在kth读计算机硕士，不吸烟，男孩6岁。整洁，爱干净。');
		$element->setRequired(true);
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Textarea(AdvertisingAgency::COMMENT);
		$element->setAttrib('rows',6);
		$element->setLabel("对房子的要求？最高房租？最小面积？地点？入住时间？");
		$element->setDescription('如：最好是一室一厅的整个公寓，也可以与其他人合租。可以合用厨房卫生间。最高房租10000kr每月，最小面积30平米。地点只要是地铁附近，不要离中心太远都可以。入住时间是9月1日，希望能长租。');
		$element->setRequired(true);
		$this->addElement($element);
		
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$element = new Zend_Form_Element_Submit("post");
		$this->addElement($element);
		
		$this->addDisplayGroup(array(AdvertisingAgency::DESCRIPTION,AdvertisingAgency::COMMENT,'challenge','post'),'aboutRoom');
		
		
		$this->getDisplayGroup('aboutRoom')->removeDecorator('DtDdWrapper');
		
	}
}
?>