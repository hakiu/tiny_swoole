<?php
/**
 * File: Task.php
 * Author: 大眼猫
 */

abstract class Task {

	public static function add($args){
		Server::$instance->task($args);
	}

	public static function onTask(swoole_server $server, int $task_id, int $worker_id, $args) {
		$controller = $args['controller'];
		$action     = $args['action'];
		$data       = $args['data'];

		$instance = Helper::import($controller);
        if($instance !== FALSE){
            if(method_exists($instance, $action)){
                $instance->$action($data);
                $server->finish('__FINISH__');
            }else{
            	$error = 'Method '.$action.' NOT found !';
            	Helper::raiseError(debug_backtrace(), $error);
            }
        }
	}

	public static function onFinish(swoole_server $server, int $task_id, string $data){
		echo $data.PHP_EOL;
	}
}
