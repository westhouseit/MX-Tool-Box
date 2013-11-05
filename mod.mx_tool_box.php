<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * -
	 * @package		MX Tool Box
	 * @subpackage	ThirdParty
	 * @category	Modules
	 * @author    Max Lazar <max@eec.ms>
	 * @copyright Copyright (c) 2010-2011 Max Lazar (http://eec.ms)
	 * @link		http://eec.ms/
	 */
class Mx_tool_box {

	var $return_data;
	
	function Mx_tool_box()
	{		
		$this->EE =& get_instance(); // Make a local reference to the ExpressionEngine super object
	}
	
		
	/**
     * Helper function for getting a parameter
	 */		 
	function _get_param($key, $default_value = '')
	{
		$val = $this->EE->TMPL->fetch_param($key);
		
		if($val == '') {
			return $default_value;
		}
		return $val;
	}

	/**
	 * Helper funciton for template logging
	 */	
	function _error_log($msg)
	{		
		$this->EE->TMPL->log_item("mx_tool_box ERROR: ".$msg);		
	}		
}

/* End of file mod.mx_tool_box.php */ 
/* Location: ./system/expressionengine/third_party/mx_tool_box/mod.mx_tool_box.php */ 
