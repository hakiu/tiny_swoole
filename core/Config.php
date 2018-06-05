<?php
/**
 * File: Config.php
 * Author: 大眼猫
 */

abstract class Config {

	public static function getConfig($section = '') {
		$config = include CONF_PATH.'/config.php';
		if($section){
			return $config[$section];
		}else{
			return $config;
		}
	}
}
