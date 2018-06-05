<?php
/**
 * File: TinySwoole
 * Author: 大眼猫
 */

class TinySwoole {

	private $min_version = '7.0';
	private $tinySwoole_version = 1.0;
	private $extensions  = ['pdo', 'redis', 'swoole', 'pdo_mysql'];

	function __construct(){

	}

	public function bootstrap(){
		$this->checkSapi();
		$this->checkVersion();
		$this->checkExtension();

		return $this;
	}

	// Only run in CLI
	private function checkSapi(){
		$sapi_type = php_sapi_name();
		if (strtoupper($sapi_type) != 'CLI') {
		    echo 'NOT CLI MODE'; die;
		}

		return TRUE;
	}

	// PHP Version must be greater then 7.0
	private function checkVersion(){
		$retval = version_compare(PHP_VERSION, $this->min_version);
		if(-1 == $retval){
			echo 'PHP version must be greater then 7.0'; die;
		}

		return TRUE;
	}

	// Must install necessary extensions
	private function checkExtension(){
		foreach($this->extensions as $extension){
			if(!extension_loaded($extension)){
				echo 'Extension '.$extension.' is required '; die;
			}
		}

		return TRUE;
	}

	// Init
	public function init(){
		define('TB_PK',    'id');
		define('APP_NAME', 'Tiny_Swoole');
		define('CORE_PATH', APP_PATH.'/core');
		define('CONF_PATH', APP_PATH.'/conf');
		define('ENV', strtoupper(ini_get('swoole.environ')));

		require_once CORE_PATH.'/Task.php';
		require_once CORE_PATH.'/Timer.php';
		require_once CORE_PATH.'/Pool.php';
		require_once CORE_PATH.'/Model.php';
		require_once CORE_PATH.'/Server.php';
		require_once CORE_PATH.'/Logger.php';
		require_once CORE_PATH.'/Helper.php';
		require_once CORE_PATH.'/Config.php';
		require_once CORE_PATH.'/Function.php';
		require_once CORE_PATH.'/Controller.php';
		return $this;
	}

	// Let's go
	public function run(){
		$config = Config::getConfig();

		// TO-DO: 混合监听
		if($config['websocket']['enable']){
			require CORE_PATH.'/WebSocket.php';
			$server = new WebSocket($config);
		}else if($config['tcp']['enable']){
			require CORE_PATH.'/TcpServer.php';
			$server = new TcpServer($config);
		}else if($config['http']['enable']){
			require CORE_PATH.'/HttpServer.php';
			$server = new HttpServer($config);
		}

		$server->start();
	}
}
