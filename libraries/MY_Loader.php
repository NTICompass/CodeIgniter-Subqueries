<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader{

	function __construct(){
		parent::__construct();
	}

	/**
	 * MY_Database Loader
	 *
	 * FROM: http://codeigniter.com/wiki/Extending_Database_Drivers/
	 */
	function database($params = '', $return = FALSE, $active_record = FALSE){
		// Do we even need to load the database class?
		if(class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE AND isset($CI->db) AND is_object($CI->db)){
			return FALSE;
		}

		require_once(BASEPATH.'database/DB'.EXT);

		// Load the DB class
		$db =& DB($params, $active_record);

		$my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
		$my_driver_file = APPPATH.'libraries/'.$my_driver.EXT;

		if(file_exists($my_driver_file)){
			require_once($my_driver_file);
			$db =& new $my_driver(get_object_vars($db));
		}

		if($return === TRUE){
			return $db;
		}
		// Grab the super object
		$CI =& get_instance();

		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		$CI->db = '';
		$CI->db = $db;

		// Assign the DB object to any existing models
		$this->_ci_assign_to_models();
	}
}
?>
