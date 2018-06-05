<?php

//异步客户端

$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

$client->on("connect", function($cli){
    $d = [];
    $d['controller'] = 'user';
    $d['action']     = 'redis';
    $d['key']        = 'foo';

    $cli->send(json_encode($d)."\r\n");
});

$client->on("receive", function(swoole_client $cli, $data){
    echo PHP_EOL.$data."\n".PHP_EOL;
});

$client->on("error", function(swoole_client $cli){
    echo "error".PHP_EOL;
});

$client->on("close", function(swoole_client $cli){
    echo "Connection close".PHP_EOL;
});

$client->connect('192.168.1.31', 9500);