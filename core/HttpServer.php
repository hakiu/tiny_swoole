<?php
/**
 * HttpServer
 */

class HttpServer {

    private $server;

    public function __construct($config) {
        $ip   = $config['http']['ip'];
        $port = $config['http']['port'];
        $this->server = new swoole_http_server($ip, $port);

        $c = [];
        foreach($config['common'] as $key => $val){
            $c[$key] = $val;
        }

        $this->server->set($c);
        $this->server->on('task',          ['Task', 'onTask']);
        $this->server->on('finish',        ['Task', 'onFinish']);
        $this->server->on('request',       [$this, 'onRequest']);
        $this->server->on('WorkerStop',    [$this, 'onWorkerStop']);
        $this->server->on('WorkerStart',   [$this, 'onWorkerStart']);
        $this->server->on('ManagerStart',  [$this, 'onManagerStart']);

        Server::$type = Server::TYPE_HTTP;
        Server::$instance = $this->server;
    }

    public function onWorkerStop(swoole_server $server, int $worker_id){
        Logger::save('Worker '.$worker_id.' stop'.PHP_EOL);
    }

    public function onWorkerStart(swoole_server $server, int $worker_id) {
        if ($server->taskworker) {
            $process_name = APP_NAME.'_http_task';
        }else{
            $process_name = APP_NAME.'_http_worker';
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

    // 将请求转至 Controller => Action
    public function onRequest($request, $response) {
        $method = $request->server['request_method'];
        if($method == 'GET'){
            if(isset($request->get['controller'])){
                $controller = $request->get['controller'];
                $action     = $request->get['action'];
            }
        }else if($method == 'POST'){
            if(isset($request->post['controller'])){
                $controller = $request->post['controller'];
                $action     = $request->post['action'];
            }
        }else{
            $response->end('Only GET and POST supported now !');
        }

        !$controller && $controller = 'index';

        if($controller){
            $instance = Helper::import($controller);

            if($instance !== FALSE){
                $instance->request  = $request;
                $instance->response = $response;

                !$action && $action = 'index';
                $instance->$action();
            }else{
                $response->status('404');

                $rep['code']  = 0;
                $rep['error'] = 'Controller '.$controller.' not found';
                $response->end(JSON($rep));
            }
        }
    }

    public function onClose(swoole_server $server, int $fd, int $reactor_id){
        Logger::save('Http client '.$fd.' closed'.PHP_EOL);
    }

    public function onManagerStart(swoole_server $server){
        swoole_set_process_name(APP_NAME.'_http_manager');
    }

    public function start() {
        swoole_set_process_name(APP_NAME.'_http_master');
        $this->server->start();
    }
}