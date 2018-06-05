<?php
/**
 * File: Controller.php
 * Author: 大眼猫
 */

abstract class Controller {

	public $fd;       // fd for websocket and tcp
	public $server;   // server object for websocket and tcp
	public $data;     // client data for websocket and tcp, array format

	public $request;  // http request
	public $response; // http response

	// TCP 和 WEB_SOCKET 输出数据给客户端, HTTP 的则不适用
	protected function response($data){
		switch(Server::$type){
			case Server::TYPE_TCP:
				$this->server->send($this->fd, $data);
			break;

			case Server::TYPE_WEB_SOCKET:
				$this->server->push($this->fd, $data);
			break;
		}
	}

	// 获取 http GET 中的参数
	protected function get($key){
		return $this->request->get[$key];
	}

	// 获取 http POST 中的参数
	protected function getPost($key){
		return $this->request->post[$key];
	}

	// 获取 http COOKIE 中的参数
	protected function getCookie($key){
		return $this->request->cookie[$key];
	}

	// 获取 http HEADER 中的参数
	protected function getHeader($key){
		return $this->request->header[$key];
	}

	// 获取 http SERVER 中的参数
	protected function getServerInfo($key){
		return $this->request->serverInfo[$key];
	}

	public function __call($name, $arguments){
		$rep['code']  = 0;
		$rep['error'] = 'Method '.$name.' not found';

		switch(Server::$type){
			case Server::TYPE_HTTP:
				$this->response->status('404');
				$this->response->end(JSON($rep));
			break;

			case Server::TYPE_TCP:
				$this->server->send($this->fd, JSON($rep));
			break;

			case Server::TYPE_WEB_SOCKET:
				$this->server->push($this->fd, JSON($rep));
			break;
		}
	}
	
}