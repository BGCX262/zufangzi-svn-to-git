<?php

class My_Validator_FormValueNotNull extends Zend_Validate_Abstract {
	const NOT_MATCH = 'notMatch';
	
	protected $_messageTemplates = array (self::NOT_MATCH => 'Value is required and can\'t be empty' );
	
	public function isValid($value, $context = null) {
		$value = ( string ) $value;
		$this->_setValue ( $value );
		if (!empty($value)) {
			return true;
		}
		
		$this->_error ( self::NOT_MATCH );
		return false;
	}
}
?>