<?php
/**
 * File: Pool.php
 * Author: 大眼猫
 */

abstract class Pool {

	private static $pool;

	public static function getInstance($type) {
		if(!isset(self::$pool[$type])){
			self::$pool[$type] = self::connect($type);
		}

		return self::$pool[$type];
	}

	private static function connect($type){
		$config = Config::getConfig($type);

		if(strtoupper($type) == 'MYSQL'){
			$db   = $config['db'];
	        $host = $config['host'];
	        $user = $config['user'];
	        $port = $config['port'];
	        $pswd = $config['pwd'];

	        $dsn = 'mysql:host='.$host.';port='.$port.';dbname='.$db;

	        try {
	            $mysql = new PDO($dsn, $user, $pswd);
	        } catch (PDOException $e) {
	        	Helper::raiseError(debug_backtrace(), $e->getMessage());
	            return FALSE;
	        }

	        $mysql->query('SET NAMES utf8');
	        return $mysql;
		}else if(strtoupper($type) == 'REDIS'){
	        $redis  = new \Redis();
	        $retval = $redis->connect($config['host'], $config['port']);
	        if(!$retval){
	            return FALSE;
	        }

	        if($config['pwd']){
	        	$auth_retval = $redis->auth($config['pwd']);
	        	if(!$auth_retval){
	        		return FALSE;
	        	}
	        }

	        $config['db'] && $redis->select($config['db']);

	        return $redis;
		}
	}
}
