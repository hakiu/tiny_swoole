<?php
/**
 * File: Helper.php
 * Functionality: Model, function loader, raiseError, generateSign, response
 * Author: 大眼猫
 * Date: 2013-5-8
 */

abstract class Helper {

	private static $controllers;

	public static function import($controller){
		$hash = md5($controller.'_'.Server::$type);
		if(!isset(self::$controllers[$hash])){
			$file = APP_PATH .'/controller/'.Server::$type.'/'.ucfirst($controller).'.php';

			if(file_exists($file)){
				require_once $file;
				$class = 'C_'.$controller;
				self::$controllers[$hash] = new $class();
			}else{
				$error = 'No such file or directory: '.$file;
				self::raiseError(debug_backtrace(), $error);
				return FALSE;
			}
		}

		return self::$controllers[$hash];
	}

	/**
	 * Load model
	 * <br />After loading a model, the new instance will be added into $obj immediately,
	 * <br />which is used to make sure that the same model is only loaded once per request !
	 *
	 * @param string => model to be loaded
	 * @param string => DB to use: master or slave
	 * @return new instance of $model or raiseError on failure !
	 */
	public static function load($model){
		$default = FALSE;
		$file = APP_PATH .'/model/'.ucfirst($model).'.php';

		if(!file_exists($file)) {
			// 加载默认模型, 减少没啥通用方法的模型
			$default = TRUE;
			$table   = strtolower($model);
			$model   = 'Default';
			$file    = APP_PATH.'/model/Default.php';
		}

		require_once $file;
		$model = 'M_'.$model;

		if($default){
			$class = new $model($table);
		}else{
			$model = $model;
			$class = new $model();
		}
		
		return $class;
	}

	/**
	 * Raise error if it is under DEV
	 *
	 * @param string debug back trace info
	 * @param string error to display
	 * @param string error SQL statement
	 * @return null
	 * @remark: trace 进一步处理
	 */
	public static function raiseError($trace, $error, $sql = '') {
		echo '=============================== Error ============================='.PHP_EOL;
        echo 'Environ: '.ENV.PHP_EOL;
        echo 'Error: '.$error.PHP_EOL.PHP_EOL;
        foreach($trace as $t){
        	if(isset($t['file'])){
	        	echo 'File: ' .$t['file'] .PHP_EOL;
	        	echo 'Function: ' .$t['function'] .PHP_EOL;
	        	echo 'Line: ' .$t['line'] .PHP_EOL.PHP_EOL;
	        }
        }

        if($sql){
			echo 'SQL: ' .$sql .PHP_EOL;     	
        }

        echo '=================================================================='.PHP_EOL;
	}
}
