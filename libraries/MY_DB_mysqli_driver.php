<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver{

	var $CI;
	var $db = NULL;
	var $subquery = FALSE;
	var $statement = '';

	function __construct($params){
		parent::__construct($params);
		$this->CI =& get_instance();
	}

	function start_subquery($statement){
		$this->subquery = TRUE;
		$this->db = $this->CI->load->database('', true);
		$this->statement = $statement;
	}

	function end_subquery($alias){
		$sql = $this->db->_compile_select();
		$statement = $this->statement;
		parent::$statement("($sql) AS $alias");

		$this->subquery = FALSE;
		$this->db = NULL;
		$this->statement = '';
	}

	/*******************************************/

	function select($select='*', $escape=NULL){
		if($this->subquery){
			$this->db->select($select, $escape);
			return $this;
		}
		return parent::select($select, $escape);
	}

	function from($from){
		if($this->subquery){
			$this->db->from($from);
			return $this;
		}
		return parent::from($from);
	}

	function where($key, $value=NULL, $escape=TRUE){
		if($this->subquery){
			$this->db->where($key,$value,$escape);
			return $this;
		}
		return parent::where($key,$value,$escape);
	}

}

?>
