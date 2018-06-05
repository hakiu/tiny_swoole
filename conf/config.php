<?php

$config = [
	'common' => [
		'user'                     => 'www',
		'group'                    => 'www',
		'backlog'                  => 128,
		'daemonize'                => 0,
		'worker_num'               => 4,
		'task_ipc_mode'            => 1,
		'task_worker_num'          => 2,
		'open_tcp_nodelay'         => 0,
		'open_mqtt_protocol'       => 0,
		'open_cpu_affinity'        => 1,
		'dispatch_mode'            => 3,
		'heartbeat_idle_time'      => 120,
		'heartbeat_check_interval' => 60,
		'open_eof_check'           => TRUE,
		'package_eof'              => "\r\n",
		'package_length_type'      => 'N',
		'package_length_offset'    => 8,
  		'package_body_offset'      => 16,
		// 'pid_file' => APP_PATH.'/pid/swoole.pid',
		// 'log_file' => APP_PATH.'/log/swoole_'.date('Y-m-d').'.log',
		'pid_file'  => '/tmp/swoole.pid',
		'log_level' => 3,
		'log_file'  => '/tmp/swoole_'.date('Y-m-d').'.log',
	],

	'tcp' => [
		'enable' => TRUE,
		'ip'     => '192.168.1.31',
		'port'   => 9500,
	],

	'websocket' => [
		'enable' => FALSE,
		'ip'     => '192.168.1.31',
		'port'   => 9501,
	],

	'http' => [
		'enable' => FALSE,
		'ip'     => '192.168.1.31',
		'port'   => 9502,
	],

	'mysql' => [
		'db'   => 'test',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => 'root',
		'pwd'  => '123456',
	],
	
	'redis' => [
		'db'   => '0',
		'host' => '192.168.1.31',
		'port' => 6379,
		'pwd'  => '123456',
	],
];

return $config;