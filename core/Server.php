<?php
/**
 * File: Server.php
 * Author: 大眼猫
 */

abstract class Server {

	public static $type;
	public static $instance;

	const TYPE_TCP        = 'tcp';
	const TYPE_HTTP       = 'http';
	const TYPE_WEB_SOCKET = 'websocket';
}