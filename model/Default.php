<?php

class M_Default extends Model {

	function __construct($table) {
		$this->table = $table;
		parent::__construct();
	}

}