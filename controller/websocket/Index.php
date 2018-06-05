<?php

class C_Index extends Controller {

	private $m_user;
	private $m_news;
    
    function __construct(){
    	$this->m_user = Helper::load('User');
    	$this->m_news = Helper::load('News');
    }
    
    // Websocket onOpen 就写这里, 非必须, 如果不需要则去掉也OK
    public function onOpen(){
        Logger::save('Websocket client '.$this->fd. ' open !'.PHP_EOL);

        /*
        $news  = $this->m_news->SelectOne();
        $users = $this->m_user->SelectAll();

        $this->response(__METHOD__);
        $this->response(JSON($news));
        $this->response(JSON($users));
        $this->response(JSON($this->data));
        $this->response(__METHOD__);
        */
    }

    // Websocket onClose 就写这里, 非必须, 如果不需要则去掉也OK
    public function onClose(){
        Logger::save('Websocket client '.$this->fd. ' closed !'.PHP_EOL);
    }
}