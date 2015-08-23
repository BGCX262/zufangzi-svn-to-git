<?php
class Constant {
	const USER_DATA = 'userData';
	const VAR_USER_DATA_CITY = 'city';
	const SEARCH_DATA = 'searchData';
	const VAR_SEARCH_DATA_TYPE = 'type';
	const VAR_SEARCH_DATA_RENT = 'rent';
	const VAR_SEARCH_DATA_START_DATE = 'start_date';
	const VAR_SEARCH_DATA_CHECKIN_DATE = 'checkin_date';
	const VAR_SEARCH_DATA_CHECKOUT_DATE = 'checkout_date';
	const VAR_SEARCH_DATA_RENT_MEASUREMENT = 'rent_measurement';
	const VAR_SEARCH_DATA_STATUS = 'status';
	const VAR_SEARCH_DATA_SORTEDBY = 'sortedBy';
	const VAR_SEARCH_DATA_KEYWORD = 'keyword';
	
	// default check out date, long-term
	const DEFAULT_CHECKOUT_DATE = "2999-12-31";
	
	const SYSTEM_MAIL = 'no-reply@zugefangzi.com';
	const ADVERTISING_AGENCY_RECIPIENTS = 'info@zugefangzi.com';
	const EMAIL_DOMAIN = "zugefangzi.com";
	const EMAIL_FAIL_MESSAGE = "Failed";
	/** 
	 * This is used when the mail need to send to @zugefangzi.com itself domain,
	 * We should use another domain instead, according to seo rule,
	 * To avoid to be labeled as spam.
	 */
	const EMAIL_TO_SERVER = 'lhj1982@gmail.com';
	
	const NOTIFICATION_DAY = 14;
	const CLOSE_NOTIFICATION = "close_notification";
}
?>