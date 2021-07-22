<?php

namespace app\gatewaypush\controller;

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{

    public static function onWorkerStart($businessWorker)
    {
        echo "WorkerStart\n";
    }
    /**
     * 当客户端连接时触发 当客户端连接上gateway进程时(TCP三次握手完毕时)触发的回调函数。
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据
        $register = [
            'data' => [
                'client_id' => $client_id,
            ],
            'type' => 'register',
        ];
        Gateway::sendToClient($client_id, json_encode($register));
        // // 向所有人发送
        // Gateway::sendToAll("$client_id login\r\n");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 完整的客户端请求数据
     */
    public static function onMessage($client_id, $message)
    {
        // 向所有人发送
        // Gateway::sendToAll("$client_id said $message\r\n");
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        // 向所有人发送
        // GateWay::sendToAll("$client_id logout\r\n");
    }

    public static function onWorkerStop($businessWorker)
    {
       echo "WorkerStop\n";
    }
}
