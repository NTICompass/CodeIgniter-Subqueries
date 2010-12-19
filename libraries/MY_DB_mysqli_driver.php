<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver{

	var $CI;
	var $db = NULL;
	var $statement = '';

	function __construct($params){
		parent::__construct($params);
		$this->CI =& get_instance();
	}

	function start_subquery($statement){
		$this->db = $this->CI->load->database('', true);
		$this->statement = $statement;
		return $this->db;
	}

	function end_subquery($alias){
		$sql = $this->db->_compile_select();
		$statement = $this->statement;
		parent::$statement("($sql) AS $alias");

		$this->db = NULL;
		$this->statement = '';
	}
}

?>
