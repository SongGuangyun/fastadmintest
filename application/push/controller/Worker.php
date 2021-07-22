<?php

namespace app\push\controller;

use Exception;
use think\Log;
use think\Cache;
use Workerman\Worker;
use think\worker\Server;
use Workerman\Lib\Timer;


// 这是workerman
class WsWorker extends Server
{
    protected $protocol  = 'websocket';
    protected $host      = '0.0.0.0';
    protected $port      = '23461';
    protected $processes = 1;  //向客户端发送数据只能支持单进程
    protected $innerPort = '5678';
    protected $redis = null;
    protected $task = null;

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        echo "WorkerStart\n";
        $this->redis = Cache::store('redis')->handler();
        // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
        $inner_text_worker = new Worker('text://' . $this->host . ':' . $this->innerPort);
        $inner_text_worker->onMessage = [$this, 'innerWorkerOnMessage'];
        $this->worker->uidConnections = [];
        // ## 执行监听 ##
        $inner_text_worker->listen();
    }



    public function innerWorkerOnMessage($connection, $buffer)
    {
        // $data数组格式
        $data = json_decode($buffer, true);
        $this->task = $data['type']; // 根据type获取uid组
        $result = $this->sendMessageByUid($this->task, $buffer);
        // 返回推送结果给服务端
        $send['status'] = $result ? 'ok' : 'fail';
        $connection->send(json_encode($send));
    }


    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        echo 'onConnect方法触发 id为' . $connection->id . PHP_EOL;
    }

    //  服务端发送给指定uid
    public function sendMessageByUid($task, $message)
    {
        $key = $task . '_uids';
        $uids = $this->redis->smembers($key);
        if (!empty($uids)) {
            foreach ($uids as $id) {
                if (isset($this->worker->uidConnections[$id])) {
                    $connection = $this->worker->uidConnections[$id];
                    $connection->send($message);
                } else {
                    $this->redis->srem($key, $id);
                }
            }
            return true;
        }
        return false;
    }



    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $message = json_decode($data, true);
        $this->redis->sadd($message['type'] . '_uids', $connection->id);
        $this->worker->uidConnections[$connection->id] = $connection;
        $sendMsg = [
            'type' => $message['type'],
            'uid' => $connection->id,
            'content' => '连接成功',
        ];
        $connection->send(json_encode($sendMsg));
        // try {
        //     $message = [
        //         'content' => null,
        //         'status' => 0
        //     ];
        //     $message = json_decode($data, true);
        //     var_dump($message);
        //     switch ($message['type']) {
        //         case 'index_info':
        //             $sendMsg['type'] = 'index_info';
        //             $sendMsg['admin_id'] = $message['admin_id'] ?? 0;
        //             $this->indexInfo($connection, $sendMsg);
        //             break;
        //         default:
        //             break;
        //     }
        // } catch (Exception $e) {
        //     Log::error('workerman onMessage failed' . json_encode($e->getMessage()));
        // }
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
        var_dump('关闭连接：id为' . $connection->id);
        if (isset($connection->id)) {
            // 连接断开时删除映射
            unset($this->worker->uidConnections[$connection->id]);
            $this->redis->srem($this->task . '_uids', $connection->id);
        }
    }


    public function indexInfo($connection, $sendMsg)
    {
        if ($sendMsg['admin_id'] == 0) {
            $sendMsg['status'] = -1;
            $sendMsg['content'] = '请先登录！';
            $connection->send(json_encode($sendMsg));
        } else {
            Timer::add(5, function () use ($sendMsg, $connection) {
                $sendMsg['content'] = $this->info($sendMsg['admin_id']);
                $connection->send(json_encode($sendMsg));
            });
        }
    }
}
