<?php

class M_User extends Model {
    
    function __construct(){
        $this->table = 'user';
        parent::__construct();
    }

    public function SelectAll(){
        $field = ['id', 'username', 'password'];
        return $this->Field($field)->Select();
    }
}