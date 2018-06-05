<?php

class C_User extends Controller {

	private $m_user;
	private $m_news;
    
    function __construct(){
    	$this->m_user = Helper::load('User');
    	$this->m_news = Helper::load('News');
    }
    
    public function index(){
        $this->response(JSON($this->data));

        // Task
        $args = [];
        $args['controller']   = 'user';
        $args['action']       = 'myTask';
        $args['data']['line'] = __LINE__;
        $args['data']['type'] = Server::$type;
        Task::add($args);
    }

    public function news(){
        $news = $this->m_news->SelectOne();
        $this->response(JSON($news));

        // Timer for test
        Timer::add(2000, [$this, 'tick'], [__LINE__, Server::$type]);

        // After timer
        Timer::after(5000, [$this, 'after']);
    }

    public function tick(int $timerID, $args){
        $this->response('Time in tick '.date("Y-m-d H:i:s\n"));
        $this->response('Args in tick '.JSON($args));

        # Clear timer
        Timer::clear($timerID);
    }

    // Timer after for test
    public function after(){
        $this->response('Execute '.__METHOD__.' in after timer');
    }

    public function myTask($args){
        echo __METHOD__;
        pr($args);
    }

    public function user(){
        $field = ['id', 'username'];
        $order = ['id' => 'DESC'];
        $users = $this->m_user->Field($field)->Order($order)->Select();
        $this->response(JSON($users));

        $users = $this->m_user->SelectAll();
        $this->response(JSON($users));

        $user = $this->m_user->SelectByID('', 1);
        $this->response(JSON($user));
    }

    public function redis(){
        $key = $this->data['key'];
        if($key){
    	    $this->response(Pool::getInstance('redis')->get($key));
        }else{
            $this->response('Key is required !');
        }
    }
}