# tiny_swoole

#### 极简Swoole 
> TCP, WEBSOCKET, HTTP <br />
> Timer, Task 简易封装 <br />
> MC 模式处理请求 <br />
> shell 脚本启动, 停止, 停止服务

#### 环境要求
> PHP >= 7.0 <br />
> swoole <br />
> pdo <br />
> redis <br />
> pdo_mysql <br />

#### 安装
> Git clone 至任一目录

#### CLI 命令
> 启动: sh shell/socket.sh start <br />
> 状态: sh shell/socket.sh status <br />
> 停止: sh shell/socket.sh stop <br />
> 重启: sh shell/socket.sh restart <br />
> Reload: sh shell/socket.sh reload <br />

#### 配置
> 配置文件是 conf/config.php <br />
> common 为公共配置部分, 影响整体 <br />
> tcp, http, websocket 为独立部分, 暂不支持混和监听, 也就是当前版本只能启动三个中的一个服务 <br />
> mysql, redis 配置 <br />

#### 使用
> 采用 MC 模式, 所有的请求均转至当前服务的 Controller <br />
> Controller 中加载 Model 操作数据库 <br />

#### TCP 服务
> config.php 将 tcp 的 enable 设置为 true <br />
> sh shell/socket.sh restart 重启服务 <br />
> ps -ef | grep Tiny 将看到 <br />
>> Tiny_Swoole_tcp_master: 为 master 进程  <br />
>> Tiny_Swoole_tcp_manager: 为 manager 进程<br />
>> Tiny_Swoole_tcp_task: N 个 task 进程 <br />
>> Tiny_Swoole_tcp_worker: N 个 worker 进程 <br /><br />
> controller/tcp 目录下有一个 Index.php, 负责处理 onConnect, onClose 事件<br />
> 为了将控制权由 onReceive 转至对应的控制器, 客户端发送的数据需要指定处理该请求的 controller 及 action, 比如要指定由 User 控制器下的 news Action来处理, 则发送的数据中应该是这样的 json 格式: 【参见 client/tcp_client.php】
```
	$data = [];
	$data['controller'] = 'user';
	$data['action']     = 'news';
	$data['key']        = 'foo'; // 其他参数
	$cli->send(json_encode($d)."\r\n");
```
>> 1：如果 action 为空, 则由 index() 处理 <br />
>> 2：如果 action 不存在或不能调用, 则客户端收到 Method $name not found <br />
>> 3：如果 controller 不存在或不能实例化, 则客户端收到 Controller $controller not found <br />

> controller/tcp/控制器中的 $this->data 为客户端发过来的完整数据
> controller/tcp/控制器的方法中调用 $this->response($data) 将数据发送至客户端

#### Websocket 服务
> config.php 将 websocket 的 enable 设置为 true <br />
> sh shell/socket.sh restart 重启服务 <br />
> ps -ef | grep Tiny 将看到 <br />
>> Tiny_Swoole_websocket_master: 为 master 进程  <br />
>> Tiny_Swoole_websocket_manager: 为 manager 进程<br />
>> Tiny_Swoole_websocket_task: N 个 task 进程 <br />
>> Tiny_Swoole_websocket_worker: N 个 worker 进程 <br /><br />

> controller/websocket 目录下有一个 Index.php, 负责处理 onOpen, onClose 事件 <br />
> 为了将控制权由 onMessage 转至对应的控制器, 客户端发送的数据需要指定处理该请求的 controller 及 action, 比如要指定由 User 控制器下的 news Action来处理, 则发送的数据中应该是这样的 json 格式: 【参见 client/websocket.html】
```
	var controller = $('#controller').val();
    var action     = $('#action').val();
    var key        = $('#key').val();

    var arr = {};
    arr.controller = controller;
    arr.action     = action;
    arr.key        = key;
    // 组成成 JSON 发过去
    ws.send(JSON.stringify(arr));
```
>> 1：如果 action 为空, 则由 index() 处理 <br />
>> 2：如果 action 不存在或不能调用, 则客户端收到 Method $name not found <br />
>> 3：如果 controller 不存在或不能实例化, 则客户端收到 Controller $controller not found <br />

> controller/websocket/控制器中的 $this->data 为客户端发过来的完整数据<br />
> controller/websocket/控制器的方法中调用 $this->response($data) 将数据发送至客户端

#### HTTP 服务
> config.php 将 http 的 enable 设置为 true <br />
> sh shell/socket.sh restart 重启服务 <br />
> ps -ef | grep Tiny 将看到 <br />
>> Tiny_Swoole_http_master: 为 master 进程  <br />
>> Tiny_Swoole_http_manager: 为 manager 进程<br />
>> Tiny_Swoole_http_task: N 个 task 进程 <br />
>> Tiny_Swoole_http_worker: N 个 worker 进程 <br /><br />

> controller/http 目录下有一个 Index.php, 其中的index()方法处理首页事件 <br />
> 为了将控制权由 onRequest 转至对应的控制器, 客户端发送的数据需要指定处理该请求的 controller 及 action, 比如要指定由 User 控制器下的 news Action来处理, 则URL应该是这样的格式: 
```
	http://$ip:$port/?controller=user&action=news
```
> 带上key=foo参数
```
	http://$ip:$port/?controller=user&action=redis&key=foo
```

>> 1：如果 action 为空, 则由 index() 处理 <br />
>> 2：如果 action 不存在或不能调用, 则客户端收到 Method $name not found <br />
>> 3：如果 controller 不存在或不能实例化, 则客户端收到 Controller $controller not found <br />

> $this->get($key), $ths->getPost($key) 获取 GET / POST 的参数 <br />
> controller/http/控制器的方法中调用 $this->response->write($data) 将数据发送至客户端和 $this->response->end($data) 结束发送;

#### MySQL
> 通过模型访问数据库<br />
> 控制器中使用 $this->m_user = Helper::load('User'); 加载 User 模型<br />
> 使用链式操作 Filed($field)->Where($where)->Order($order)->Limit($limit) 构建 SQL<br />
> SelectOne(), Select(), UpdateOne(), Update(), DeleteOne(), Delete()<br />
> 根据ID 查询: SelectByID(), SelectFieldByID()<br />
> 执行复杂的 SQL: Query($sql)<br />
> beginTransaction(), Commit(), Rollback() 操作事务<br />
```
	$field = ['id', 'username'];
    $order = ['id' => 'DESC'];
    $users = $this->m_user->Field($field)->Order($order)->Select();
```

#### Redis
> Pool::getInstance('redis')->get($key) <br />
> Pool::getInstance('redis')->del($key) <br />
> Pool::getInstance('redis')->set($key, $val) <br />

#### 定时器 Timer
> 控制器中想每2秒执行当前的 tick 方法, 并且传递 xyx 作为参数, 则这样做
```
	Timer::add(2000, [$this, 'tick'], 'xyz');
```
tick 方法则这样接收
```
	public function tick(int $timerID, $args){
        $this->response('Time in tick '.date("Y-m-d H:i:s\n"));
        $this->response('Args in tick '.JSON($args));

        # Clear timer
        Timer::clear($timerID);
    }
```

> 控制器中想5秒后执行当前的 after 方法, 则这样做。 注: after定时器不接收任何参数
```
    Timer::after(5000, [$this, 'after']);
```
after 方法
```
	public function after(){
        $this->response('Execute '.__METHOD__.' in after timer');
    }
```

#### 任务投递 Task
> 控制器user中要将数据投递到 task 且由当前的 myTask 来处理业务逻辑
```
	// Task
    $args = [];
    $args['controller']   = 'user';
    $args['action']       = 'myTask';
    $args['data']['line'] = __LINE__;
    $args['data']['type'] = Server::$type;
    Task::add($args);
```
myTask 方法则这样接收参数, $args 仅包括 $args['data'] 中的数据, 不包括 controller 与 action
```
	public function myTask($args){
        echo __METHOD__."\n";
        pr($args);
    }
```