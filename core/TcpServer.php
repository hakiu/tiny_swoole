<?php
/**
 * TcpServer
 */

class TcpServer {

    private $server;

    public function __construct($config) {
        $ip   = $config['tcp']['ip'];
        $port = $config['tcp']['port'];
        $this->server = new Swoole_Server($ip, $port);

        $c = [];
        foreach($config['common'] as $key => $val){
            $c[$key] = $val;
        }
        
        $this->server->set($c);
        $this->server->on('task',          ['Task', 'onTask']);
        $this->server->on('Close',         [$this, 'onClose']);
        $this->server->on('finish',        ['Task', 'onFinish']);
        $this->server->on('Connect',       [$this, 'onConnect']);
        $this->server->on('Receive',       [$this, 'onReceive']);
        $this->server->on('WorkerStop',    [$this, 'onWorkerStop']);
        $this->server->on('WorkerStart',   [$this, 'onWorkerStart']);
        $this->server->on('ManagerStart',  [$this, 'onManagerStart']);

        Server::$type = Server::TYPE_TCP;
        Server::$instance = $this->server;
    }

    public function onWorkerStop(swoole_server $server, int $worker_id){
        Logger::save('Worker '.$worker_id.' stop'.PHP_EOL);
    }

    public function onWorkerStart(swoole_server $server, int $worker_id) {
        if ($server->taskworker) {
            $process_name = APP_NAME.'_tcp_task';
        }else{
            $process_name = APP_NAME.'_tcp_worker';
        }

        swoole_set_process_name($process_name);

        $retval = Pool::getInstance('mysql');
        if($retval === FALSE){
            echo 'Can not connect to MySQL, shutting down ...'.PHP_EOL;
            $server->shutdown(); exit(0);
        }

        $retval = Pool::getInstance('redis');
        if($retval === FALSE){
            echo 'Can not connect to Redis, shutting down ...'.PHP_EOL;
            $server->shutdown(); exit(0);
        }
    }

    // onConnect 转至 index / onConnect, 非必须
    public function onConnect(swoole_server $server, int $fd, int $reactor_id) {
        $instance = Helper::import('index');

        if($instance !== FALSE){
            if(method_exists($instance, 'onConnect')){
                $instance->fd     = $fd;
                $instance->server = $server;
                $instance->onConnect();
            }else{
                $error = 'TCP method onConnect NOT found !';
                Helper::raiseError(debug_backtrace(), $error);
            }
        }
    }

    // 将请求转至 Controller => Action
    public function onReceive(swoole_server $server, int $fd, int $reactor_id, string $json) {
        $data = json_decode($json, TRUE);
        $controller = $data['controller'];

        if($controller){
            $instance = Helper::import($controller);

            if($instance !== FALSE){
                $instance->fd     = $fd;
                $instance->data   = $data;
                $instance->server = $server;

                $action = $data['action'];
                !$action && $action = 'index';
                $instance->$action();
            }else{
                $rep['code']  = 0;
                $rep['error'] = 'Controller '.$controller.' not found';
                $server->send($fd, JSON($rep));
            }
        }
    }

    // onClose 转至 index / onClose, 非必须
    public function onClose(swoole_server $server, int $fd, int $reactor_id){
        $instance = Helper::import('index');
        if($instance !== FALSE){
            if(method_exists($instance, 'onClose')){
                $instance->fd     = $fd;
                $instance->server = $server;
                $instance->onClose();
            }else{
                $error = 'TCP method onClose NOT found !';
                Helper::raiseError(debug_backtrace(), $error);
            }
        }
    }

    public function onManagerStart(swoole_server $server){
        swoole_set_process_name(APP_NAME.'_tcp_manager');
    }

    public function start() {
        swoole_set_process_name(APP_NAME.'_tcp_master');
        $this->server->start();
    }
}