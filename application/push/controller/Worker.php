<?php

namespace app\push\controller;

use think\worker\Server;

class Worker extends Server
{
    protected $protocol  = 'websocket';
    protected $host      = '0.0.0.0';
    protected $port      = '23461';
    protected $processes = 4;

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        echo "Worker    Start\n";
    }


    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        var_dump($connection->id);
        // var_dump($connection);
    }

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $connection->send('我收到你的信息了');
    }


    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {

    }


}
