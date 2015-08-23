<?php
/**
 * SessionUtil is used for push/pop data from session.
 * There is only one session namespace in this application,
 * and it can have multiple key=>value pairs.
 * 
 * @author james
 *
 */
class SessionUtil {	
	const NAME = "myData";
	// expire in 30min
	const EXPIRE_IN_SEC = 1800;
	
	/**
	 * Get property value from session by given key.
	 * 
	 * @param $key
	 * @return return value of key
	 */
	public static function getProperty($key) {
		$myNamespace = new Zend_Session_Namespace(self::NAME);
        $myNamespace->setExpirationSeconds(self::EXPIRE_IN_SEC, $key);
        return $myNamespace->$key;
	}
	
	/**
	 * Push value to session with key.
	 * 
	 * @param $key
	 * @param $value
	 */
	public static function setProperty($key, $value) {
		$myNamespace = new Zend_Session_Namespace(self::NAME);
	   if (!isset($myNamespace->initialized)) {
            Zend_Session::regenerateId();
            $myNamespace->initialized = true;
        }
        $myNamespace->setExpirationSeconds(self::EXPIRE_IN_SEC, $key);
        $myNamespace->$key = $value;
	}
}
?>