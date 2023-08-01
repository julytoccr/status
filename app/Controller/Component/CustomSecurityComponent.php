<?php
/**
 * MySecurity.php
 * Some security things for Cake
 *
 * Features:
 * - The public functions from controller & object now can't be called from url
 * -
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 *
 * @package components
 */
class CustomSecurityComponent extends Component{
	
	public $components = array('Security');

	/**
	 * Extra forbidden actions
	 *
	 * @var array $forbidden_actions
	 */
	public $forbidden_actions=array();
	
	function initialize($controller){
		$this->forbidden_actions=am($this->forbidden_actions, get_class_methods('Controller'));
	}

	function startup($controller){
		$this->Security->startup($controller);
		if (in_array($controller->action,$this->forbidden_actions))
		{
			$this->Security->blackHoleCallback=null;
			$this->Security->blackHole($this);
		}
	}

	/**
	 * Returns if the HTTP Request was made within localhost
	 * @param array $extra_ips Array of IPs of local host machine (optional)
	 */
	function is_localhost_call($extra_ips)
	{
		$ips=am($extra_ips,array('127.0.0.1'));
		return (in_array($_SERVER['REMOTE_ADDR'],$ips));
	}
}
?>
