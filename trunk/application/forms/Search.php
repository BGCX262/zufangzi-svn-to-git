<?php
class Search extends Zend_Form {
	
	private $city;
	
	public function Search($city) {
		$this->city = $city;
		$this->__construct();
	}
	
	public function init() {
		$myNamespace = new Zend_Session_Namespace ( Constant::USER_DATA );
		$searchData = $myNamespace->searchData;
		
		$this->setMethod('POST');
		$this->setName('searchForm');
		
		$element = new Zend_Form_Element_Radio('type');
		$element->addMultiOptions(
			array(
				'all' => '全部',
				'lease' => '供',
				'want' => '求'
			)
		);
		if (!empty($searchData[Constant::VAR_SEARCH_DATA_TYPE])) {
			$element->setValue($searchData[Constant::VAR_SEARCH_DATA_TYPE]);
		} else {
			$element->setValue('all');	
		}
		$element->setSeparator('');
		$element->removeDecorator('Label');
		$this->addElement($element);
		
		
//		$element = new Zend_Form_Element_Checkbox("search_business");
//		$element->setValue(0);
//		$this->addElement($element);
		
		$element = new Zend_Form_Element_Hidden('status');
		$element->setValue('active');
		$this->addElement($element);
		
		/*$element = new Zend_Form_Element_Text('rent');
		$element->setLabel('租金');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Radio('rent_measurement');
		$element->setMultiOptions(
			array(
				'day' => '克朗每日',
				'month' => '克朗每月'
			)
		);
		$element->setSeparator('');
		$element->removeDecorator('Label');
		$element->setValue('month');
		$this->addElement($element);*/
		
		$element = new Zend_Form_Element_Text(Constant::VAR_SEARCH_DATA_CHECKIN_DATE);
		$element->setLabel('入住日期');
		//$element->setDescription('例:2010-10-20');
		if (!empty($searchData[Constant::VAR_SEARCH_DATA_CHECKIN_DATE])) {
			$element->setValue($searchData[Constant::VAR_SEARCH_DATA_CHECKIN_DATE]);
		}
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text(Constant::VAR_SEARCH_DATA_CHECKOUT_DATE);
		$element->setLabel('搬出日期(留空为长期)');
		if (!empty($searchData[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE]) && 
			$searchData[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE] != Constant::DEFAULT_CHECKOUT_DATE) {
			$element->setValue($searchData[Constant::VAR_SEARCH_DATA_CHECKOUT_DATE]);
		}
		$this->addElement($element);
		
		
		$element = new Zend_Form_Element_Text("keyword");
		$element->setLabel("关键字");
		//$element->setDescription("例：女生，lappis");
		if (!empty($searchData[Constant::VAR_SEARCH_DATA_KEYWORD])) {
			$element->setValue($searchData[Constant::VAR_SEARCH_DATA_KEYWORD]);
		}
        $this->addElement($element);
//		$element = new Zend_Form_Element_Radio('sortedBy');
//		$element->setLabel('排序');
//		$element->addMultiOptions(array(
//			'created' => '发帖时间',
//			'rent' => '房租'
//		));
//		$element->setSeparator('');
//		$element->setValue('created');
//		$this->addElement($element);
		
		
//
//		$element = new Zend_Form_Element_Hidden('city');
//		$element->setValue($this->city);
//		$element->removeDecorator('Label');
//		$this->addElement($element);
		
		$element = new Zend_Form_Element_Submit('search');
		$element->setValue('搜索');
		$element->removeDecorator('Label');
		$this->addElement($element);
	}
}
?>