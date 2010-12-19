<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver{

	var sub_select;
	var sub_from;
	var sub_join;
	var sub_where;
	var sub_like;
	var sub_group;
	var sub_having;
	var sub_order;
	var sub_set;

	function __construct($params){
		parent::__construct($params);
	}
}

?>
