<?php

class CreateOrUpdatePost extends Zend_Form {
	
	private $advertisement;
	private $password;
	private $city;
	
	public function CreateOrUpdatePost($advertisement, $password, $city) {
		$this->advertisement = $advertisement;
		$this->password = $password;
		$this->city = $city;
		$this->__construct();
	}
	
	public function init() {
		if (isset($this->advertisement)) {
			$user = Advertisement::getUser($this->advertisement->id);
			$element = new Zend_Form_Element_Hidden('id');
			$element->setValue($this->advertisement->id);
			$this->addElement($element);
			
			$element = new Zend_Form_Element_Hidden('password');
			$element->setValue($this->password);
			$this->addElement($element);
			$this->setAction('/'.strtolower($this->city->name).'/bulletin/update');
		} else {
			$this->setAction('/'.strtolower($this->city->name).'/bulletin/create');
		}
		
		$element = new Zend_Form_Element_Hidden('city');
		$element->setValue($this->city->id);
		$this->addElement($element);
			
		$this->setMethod('POST');
		$this->setName('createAdForm');
		$element = new Zend_Form_Element_Text('name');
		$element->setLabel('怎么称呼您');
		$element->setDescription('必填，中英文都可，2到30个字');
		$element->setRequired(true);		
		//$element->addValidator(new Zend_Validate_StringLength(2,30));
		if (isset($user)) {
			$element->setValue($user->name);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('email');
		$element->setLabel('您的Email');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_EmailAddress());	
		$element->addValidator('NotEmpty');
		$element->setDescription('必填，将不会显示在您的帖子里');
		if (isset($user)) {
			$element->setValue($user->email);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('mobile');
		$element->setLabel('您的手机号');		
		$element->addFilter(new Zend_Filter_HtmlEntities());
		$element->addFilter(new Zend_Filter_StripTags());
		$element->setDescription('方便其他用户联系您');
		if (isset($user)) {
			$element->setValue($user->mobile);
		}
		$this->addElement($element);
				
		
		$this->addDisplayGroup(array('name', 'email','mobile'),'aboutYou');
		$this->getDisplayGroup('aboutYou')->removeDecorator('DtDdWrapper');
		
		
		// radio box
		$element = new Zend_Form_Element_Radio('type');
		$element->addMultiOptions(
			array(
				'lease' => '供',
				'want' => '求'
			))->removeDecorator('Label');
		$element->setSeparator('')->setValue(Advertisement::LEASE);
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->type);
		}
		$this->addElement($element);
		
		// category
		$element = new Zend_Form_Element_Checkbox("isBusiness");
		$element->setLabel('是商铺/店面吗？');
		if (isset($this->advertisement) && $this->advertisement->category_id == Category::SHOP) {
			$element->setValue(1);
		} else {
			$element->setValue(0);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('title');
		$element->setLabel('帖子标题');
		$element->setRequired(true);
		$element->addValidator('NotEmpty');		
		//$element->addValidator(new Zend_Validate_StringLength(10,30));
		$element->setDescription('必填，5－100字，概括题目，突出重点');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->title);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('address');
		$element->setLabel('房子的住址');
		$element->setDescription('必填，以便用户在地图上方便的找到');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->address);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('rent');
		$element->setLabel('租金');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->rent);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Select('currency');
		//$element->setLabel("货币");
		$element->addMultiOptions(Currency::getAvailableCurrencyAsArray());
		if (isset($this->advertisement)) {
			$element->setValue(Advertisement::getCurrency($this->advertisement)->id);
		} else {
			$element->setValue(127);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Radio('rent_measurement');
		$element->addMultiOptions(
			array(
				'month' => '每月',
				'day' => '每日'
			))->setSeparator('')->setDescription('必填')->removeDecorator('Label');
		$element->setSeparator('')->setValue(Advertisement::RENT_MONTHLY);
		if (isset($this->advertisement)) {
			if ($this->advertisement->rent_measurement == Advertisement::RENT_MONTHLY) {
				$element->setValue('month');
			} else if ($this->advertisement->rent_measurement == Advertisement::RENT_DAILY) {
				$element->setValue('day');
			}
		}
		$element->setRequired(true);	
		$element->addValidator('NotEmpty');	
		$element->addValidator(new Zend_Validate_Alnum());
		$this->addElement($element);
		
		// start date and stop date
		$element = new Zend_Form_Element_Text('start_date');
		$element->setRequired(true);
		$element->addValidator('NotEmpty');
		$element->setLabel('开始日期');
		$element->setDescription('必填，格式为2010-09-11');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->start_date);
		}
		$this->addElement($element);
		$element = new Zend_Form_Element_Text('stop_date');
		$element->setLabel('结束日期');
		$element->setDescription('同上，留空则为长期');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->stop_date);
		}
		$this->addElement($element);
				
		
		$element = new Zend_Form_Element_Text('area');
		$element->setLabel('面积(平方米)');
		$element->addValidator(new Zend_Validate_Alnum());
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->area);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('num_of_room');
		$element->setLabel('房间数');
		$element->addValidator(new Zend_Validate_Alnum());
		$element->setDescription('卧房数');
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->num_of_room);
		}
		$this->addElement($element);
		
				
		$element = new Zend_Form_Element_Textarea('description');
		$element->setLabel('其他描述');
		$element->setDescription('更多详细信息，例如: 交通是否方便? 是否有独立卫生间和厨房? 是否仅限女生? 是否有家具? 月租是否包括网费? 您的个人习惯等等。');
		$element->setAttrib('rows',4);
		if (isset($this->advertisement)) {
			$element->setValue($this->advertisement->description);
		}
		$this->addElement($element);
		
		$element = new Elements();
		$element->addReCaptcha($this);
		
		$element = new Zend_Form_Element_Submit('post');
		$element->setValue('提交')->removeDecorator('Label');
		$this->addElement($element);
		
		$this->addDisplayGroup(array('isBusiness','type','title','address','rent','currency','rent_measurement','start_date','stop_date','area','num_of_room','description','challenge','post'),'aboutRoom');
		$this->getDisplayGroup('aboutRoom')->removeDecorator('DtDdWrapper');
	}
}