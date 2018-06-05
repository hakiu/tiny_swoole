<?php

class C_Index extends Controller {

	private $m_user;
	private $m_news;
    
    function __construct(){
    	$this->m_user = Helper::load('User');
    	$this->m_news = Helper::load('News');
    }
    
    // index 就写这里
    // URL: http://192.168.1.31:9502/?hello=world
    public function index(){
        $news  = $this->m_news->SelectOne();
        $users = $this->m_user->SelectAll();

        $this->response->write('<meta charset="utf8" />'.JSON($news));
        $this->response->write('Hello is => '.$this->request->get['hello']);
        $this->response->write(JSON($users));
        $this->response->end();
    }
}