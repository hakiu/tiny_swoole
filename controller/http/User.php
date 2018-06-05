<?php

class C_User extends Controller {

	private $m_user;
	private $m_news;
    
    function __construct(){
    	$this->m_user = Helper::load('User');
    	$this->m_news = Helper::load('News');
    }
    
    // URL: http://192.168.1.31:9502/?controller=user&action=index
    public function index(){
        // Task
        $args = [];
        $args['controller']   = 'user';
        $args['action']       = 'myTask';
        $args['data']['line'] = __LINE__;
        $args['data']['type'] = Server::$type;
        Task::add($args);
        
        $this->response->end(__LINE__);
    }

    public function myTask($args){
        echo __METHOD__;
        pr($args);
    }

    // URL: http://192.168.1.31:9502/?controller=user&action=news
    public function news(){
        $news = $this->m_news->SelectOne();
        $this->response->end('<meta charset="utf8" />'.JSON($news));
    }

    // URL: http://192.168.1.31:9502/?controller=user&action=user
    public function user(){
        $field = ['id', 'username'];
        $order = ['id' => 'DESC'];
        $users = $this->m_user->Field($field)->Order($order)->Select();
        $this->response->write('<meta charset="utf8" />'.JSON($users));

        $users = $this->m_user->SelectAll();
        $this->response->write('<br />'.JSON($users));

        $user = $this->m_user->SelectByID('', 1);
        $this->response->write('<br />'.JSON($user));
        
        $this->response->end();
    }

    // URL: http://192.168.1.31:9502/?controller=user&action=redis&key=foo
    public function redis(){
        $key = $this->getPost('key');
        if(!$key){
            $key = $this->get('key');
        }

        if($key){
            $val = Pool::getInstance('redis')->get($key);
    	    $this->response->end('<meta charset="utf8" />'.$val);
        }else{
            $this->response->end('<meta charset="utf8" />Key is required !');
        }
    }
}