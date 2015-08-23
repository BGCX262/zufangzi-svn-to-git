<?php
class Zend_Controller_Action_Helper_Generator extends Zend_Controller_Action_Helper_Abstract 
{
	public function direct($length = 8, $seeds = 'alpha')
	{
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwxyz';
		$seedings['numeric'] = '0123456789';
		
		$seeds = $seedings[$seeds];
		
		$random = "";
		$count = strlen($seeds);
		
		for ($x = 0; $x < $length; $x++)
		{
			$random .= $seeds[mt_rand(0, $count-1)];
		}
		
		return $random;
		
	}
	
	public function generateCurrentTime() {
		date_default_timezone_set('Europe/Stockholm');
		$sql_date_pattern = 'yyyy-MM-dd HH:mm:ss';
		$date_obj = new Zend_Date();
		$current_time = $date_obj->get($sql_date_pattern);
		return $current_time;
	}
	
	/**
	 * manipulate data and output to a string.
	 * 
	 * @param $date
	 * @param $offset
	 * @param $measure
	 * @return string
	 */
    public function manipulatDate($date, $offset, $measure) {
    	date_default_timezone_set('Europe/Stockholm');
        $sql_date_pattern = 'yyyy-MM-dd HH:mm:ss';
        $date_obj = new Zend_Date($date, $sql_date_pattern);
        $date_obj->add($offset, $measure);
        $current_time = $date_obj->get($sql_date_pattern);
        return $current_time;
    }
    
    /**
     * Generate current URI.
     * 
     * @return current URI
     */
	public function getCurrentURI() {
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	/**
	 * Get sender's ipaddress.
	 */
	public function getIpAddress() {
		$ipaddress = null;
		if (isset($_SERVER["REMOTE_ADDR"])) {
		    $ipaddress = $_SERVER["REMOTE_ADDR"];
		} else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		    $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
		    $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
		}
		return $ipaddress;
	}
}
?>