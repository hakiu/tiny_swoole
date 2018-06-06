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
> mysql, redis <br />

### 使用
> 采用 MC 模式, 所有的请求均转至当前服务的 Controller
> Controller 中加载 Model 操作数据库