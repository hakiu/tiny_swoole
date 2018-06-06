# tiny_swoole

#### 极简Swoole 
> TCP, WEB_SOCKET, HTTP <br />
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

> controller/tcp 目录下有一个 Index.php, 负责处理 onConnect, onClose 事件
> 为了将控制权由onReceive 转至对应的控制器, 客户端发送的数据需要指定处理该请求的 controller 及 action, 比如要指定由 User 控制器下的 news Action来处理, 则发送的数据中应该是这样的格式:
```
	$data = [];
	$data['controller'] = 'user';
	$data['action']     = 'news';
	$data['key']        = 'foo';
	$cli->send(json_encode($d)."\r\n");
```
>>> 如果 action 为空, 则由 index() 处理 <br />
>>> 如果 action 不存在或不能调用, 则客户端收到 Method $name not found <br />
>>> 如果 controller 不存在或不能实例化, 则客户端收到 Controller '.$controller.' not found <br />

> 