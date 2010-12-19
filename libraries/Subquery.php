<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Subquery{
	var $CI;
	var $db = array();
	var $statement = array();

	function __construct(){
		$this->CI =& get_instance();
	}

	function start_subquery($statement){
		$db = $this->CI->load->database('', true);
		$this->db[] = $db;
		$this->statement[] = $statement;
		return $db;
	}

	function end_subquery($alias=''){
		$db = array_pop($this->db);
		$sql = "({$db->_compile_select()})";
		$alias = $alias!='' ? "AS $alias" : $alias;
		$statement = array_pop($this->statement);
		if(count($this->db) == 0){
			$this->CI->db->$statement("$sql $alias");
		}
		else{
			$db = $this->db[count($this->db)-1];
			$db->$statement("$sql $alias");
		}
	}
}

?>
