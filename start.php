<?php

//引用Workerman框架（删减版）
require_once __DIR__ . '/Workerman/Autoloader.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

//创建一个worker
//监听2016端口，使用Frame协议（包长+包体）
//协议的定制在Workerman/Protocols下，为实现web端兼容，可选用WebSocket协议。也可选用Text协议（包体+\n），方便用telnet进行测试。
$worker = new Worker('frame://[::]:2016');

//当服务端收到客户端发来的数据时，触发onMessage回调
$worker->onMessage = function (TcpConnection $connection, $data) {
    //把收到的数据转发给与当前Worker建立连接的所有客户端
    foreach ($connection->worker->connections as $client_connection) {
        $client_connection->send($data);
    }
};

//启动worker
Worker::runAll();

//输入命令行 php start.php start 以启动服务
//守护进程化：php start.php start -d。使用php start.php stop以停止服务，restart以重启服务
//必要的PHP扩展：pcntl、posix（linux下安装PHP一般会自带）
//建议安装的PHP扩展：event（提高并发性能）
