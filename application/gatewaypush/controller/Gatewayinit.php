<?php

namespace app\gatewaypush\controller;

use app\gatewaypush\controller\Events;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

class Gatewayinit
{
    public $gatewayPort = 23460;
    public $registerPort = 12360;
    public function __construct()
    {
        /***************gateway*****************/
        $this->startGateway();
        /*********bussinessWorker**********/
        $this->startBusinessWorker();
        /*********register**********/
        $this->startRegister();
        // 如果不是在根目录启动，则运行runAll方法
        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }

    public function startGateway()
    {
        // Gateway进程负责网络IO
        $gateway = new Gateway("websocket://0.0.0.0:".$this->gatewayPort);
        $gateway->name = 'Gateway';  // 设置名称
        $gateway->count = 4;  // 设置进程数
        $gateway->lanIp = '127.0.0.1';  // 分布式部署时请设置成内网ip（非127.0.0.1）
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        $gateway->startPort = 2300; // 监听本机端口的起始端口
        $gateway->pingInterval = 10; // 心跳间隔
        $gateway->pingData = '{"mode":"heart"}'; // 心跳数据
        $gateway->registerAddress = '127.0.0.1:'.$this->registerPort; // 服务注册地址
        /*
        // 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
        $gateway->onConnect = function($connection)
        {
        $connection->onWebSocketConnect = function($connection , $http_header)
        {
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if($_SERVER['HTTP_ORIGIN'] != 'http://chat.workerman.net')
        {
        $connection->close();
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        // var_dump($_GET, $_SERVER);
        };
        };
         */
    }

    public function startBusinessWorker()
    {
        // BusinessWorker进程负责业务处理
        $worker = new BusinessWorker();
        $worker->name = 'BusinessWorker'; // worker名称
        $worker->count = 1; // bussinessWorker进程数量
        $worker->registerAddress = '127.0.0.1:'.$this->registerPort; // 服务注册地址
        $worker->eventHandler = Events::class; // 设置处理业务的类,此处制定Events的命名空间
    }



    /**
     * Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址
     * Gateway进程和BusinessWorker通过Register进程得到通讯地址后，就可以建立起连接并通讯了
     * 把所有Gateway的通讯地址保存在内存中
     * Register服务收到BusinessWorker的注册后，把内存中所有的Gateway的通讯地址发给BusinessWorker
     * Gateway与BusinessWorker通过Register已经建立起长连接
     * @return [type] [description]
     */
    private function startRegister()
    {
        // Register进程负责协调Gateway与BusinessWorker之间建立TCP长连接通讯 register 服务必须是text协议
        new Register('text://0.0.0.0:'.$this->registerPort);
    }
}
