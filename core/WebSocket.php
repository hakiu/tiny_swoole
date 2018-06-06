<?php
/**
 * WebSocket
 */

class WebSocket {

    private $server;

    public function __construct($config) {
        $ip   = $config['websocket']['ip'];
        $port = $config['websocket']['port'];
        $this->server = new swoole_websocket_server($ip, $port);

        $c = [];
        foreach($config['common'] as $key => $val){
            $c[$key] = $val;
        }
        
        $this->server->set($c);
        $this->server->on('task',          ['Task', 'onTask']);
        $this->server->on('finish',        ['Task', 'onFinish']);
        $this->server->on('open',          [$this, 'onOpen']);
        $this->server->on('close',         [$this, 'onClose']);
        $this->server->on('message',       [$this, 'onMessage']);
        $this->server->on('WorkerStop',    [$this, 'onWorkerStop']);
        $this->server->on('WorkerStart',   [$this, 'onWorkerStart']);
        $this->server->on('ManagerStart',  [$this, 'onManagerStart']);

        Server::$type = Server::TYPE_WEB_SOCKET;
        Server::$instance = $this->server;
    }

    public function onWorkerStop(swoole_server $server, int $worker_id){
        Logger::save('Worker '.$worker_id.' stop'.PHP_EOL);
    }

    public function onWorkerStart(swoole_server $server, int $worker_id) {
        if ($server->taskworker) {
            $process_name = APP_NAME.'_websocket_task';
        }else{
            $process_name = APP_NAME.'_websocket_worker';
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

    // onOpen 转至 index / onOpen
    public function onOpen(swoole_websocket_server $server, swoole_http_request $request) {
        $instance = Helper::import('index');

        if($instance !== FALSE){
            if(method_exists($instance, 'onOpen')){
                $instance->data   = $request;
                $instance->fd     = $request->fd;
                $instance->server = $server;
                $instance->onOpen();
            }else{
                $error = 'Websocket method onOpen NOT found !';
                Helper::raiseError(debug_backtrace(), $error);
            }
        }
    }

    // 将请求转至 Controller => Action
    public function onMessage(swoole_server $server, swoole_websocket_frame $frame) {
        $data = json_decode($frame->data, 1);
        $controller = $data['controller'];
        if($controller){
            $instance = Helper::import($controller);

            if($instance !== FALSE){
                $instance->data   = $data;
                $instance->fd     = $frame->fd;
                $instance->server = $server;

                $action = $data['action'];
                !$action && $action = 'index';
                $instance->$action();
            }else{
                $rep['code']  = 0;
                $rep['error'] = 'Controller '.$controller.' not found';
                $server->push($frame->fd, JSON($rep));
            }
        }
    }

    // onClose 转至 index / onClose
    public function onClose(swoole_server $server, int $fd, int $reactor_id) {
        $instance = Helper::import('index');
        if($instance !== FALSE){
            if(method_exists($instance, 'onClose')){
                $instance->fd     = $fd;
                $instance->server = $server;
                $instance->onClose();
            }else{
                $error = 'Websocket method onClose NOT found !';
                Helper::raiseError(debug_backtrace(), $error);
            }
        }
    }

    public function onManagerStart(swoole_server $server){
        swoole_set_process_name(APP_NAME.'_websocket_manager');
    }

    public function start() {
        swoole_set_process_name(APP_NAME.'_websocket_master');
        $this->server->start();
    }
}