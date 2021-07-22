<?php

namespace app\api\controller;

use app\model\GoodsSku;
use app\model\GoodsSpec;
use GatewayClient\Gateway;
use app\model\GoodsSpecItem;
use app\common\controller\Api;

/**
 * 示例接口
 */
class Demo extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['test', 'test1'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];

    /**
     * 测试方法
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/demo/test/id/{id}/name/{name})
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     */

    /**
     * 连接内部文本协议，传输数据发送给客户端 有GatewayClient就不需要这个了
     * @param [type] $data
     * @return void
     */
    public function connectSocketAndSend($data)
    {
        try {
            // 建立socket连接到内部推送端口
            $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
            // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
            fwrite($client, json_encode($data) . "\n");
            // 读取推送结果
            return fread($client, 8192);
        } catch (Exception $e) {
            Log::error('socket连接失败！' . $e->getMessage());
        }
    }

    /**
     * 无需登录的接口
     *
     */
    public function test1()
    {
        $data = [
            'content' => '来自服务端的给客户端的消息'
        ];
        $client_id = "7f00000108ff00000001";
        $registerPort = '12360';
        Gateway::$registerAddress = '127.0.0.1:' . $registerPort;
        Gateway::sendToClient($client_id, json_encode($data));
    }

    public function createSku()
    {
        $goods = [
            [
                'goods_id' => '23',
                'sku_num' => '1号sku',
                // 'key' => '1_2_3',
                'stock' => '121',
                'price' => '19902.1'
            ],
            [
                'goods_id' => '23',
                'sku_num' => '2号sku',
                // 'key' => '56_2_45_1',
                'stock' => '2',
                'price' => '45.1'
            ],
        ];
        $ss = GoodsSku::insertAll($goods);
        dd($ss);
    }

    public function specFormat($sku)
    {
        $skus = collection($sku)->toArray();
        $specItemKeys = array_column($skus, 'specItemIds');
        $specItemIds = array_unique(explode(':', implode(':', $specItemKeys)));
        $specItems = db('goods_spec_items')->whereIn('id', $specItemIds)->select();
        $specIds = array_unique(array_column($specItems, 'spec_id'));
        $specList = db('goods_specs')->whereIn('id', $specIds)->select();
        foreach ($specList as $key => $spec) {
            foreach ($specItems as $items) {
                if ($spec['id'] == $items['spec_id']) {
                    $specList[$key]['items'][] = $items;
                }
            }
        }
        return $specList;
    }

    // public function formatItems($id, $name)
    // {
    //     return [
    //         'id' => $id,
    //         'name' => $name,
    //     ];
    // }

    public function test()
    {
        $goods = GoodsSku::where('goods_id', 23)->select();
        foreach ($goods as $sku) {
            $sku['skus'] = $this->formatSpecAndItem($sku->specItemIds, $sku->specItems);
        }
        $this->success('ok', $goods);
        // // $specList = $this->specFormat($skus);
        // // $this->success('ok', $specList);
        // // dd($specList);
        // $specList = [
        //     [
        //         'id',
        //         'name' => 'color',
        //         'spec_item' => [
        //             'red',
        //             'white',
        //             'blue',
        //             'pink',
        //         ]
        //     ]
        // ];
        // dd($specList);
        // $goodsData['goods_attr'] = $specList;
        // $types = [
        //     [
        //         'id' => 1,
        //         'name' => 'color',
        //         'attr_values' => [
        //             'red',
        //             'white',
        //             'blue',
        //             'pink',
        //         ]
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'size',
        //         'attr_values' => [
        //             'small',
        //             'normal',
        //             'medium',
        //             'large',
        //         ]
        //     ]
        // ];
        $skus = [
            [
                'id' => 1,
                'goods_id' => 1,
                "ids" => "1-12_2-22",
                'guide_price' => 11,
                'skus' => [
                    [
                        'k' => '颜色',
                        'k_id' => 1,
                        'v' => '黑色',
                        'v_id' => 12,
                    ],
                    [
                        'k' => "内存",
                        'k_id' => 2,
                        'v' => '256G',
                        'v_id' => 22,
                    ],
                ]
            ],
            [
                'id' => 2,
                'goods_id' => 1,
                "ids" => "1-12_2-21",
                'guide_price' => 100,
                'skus' => [
                    [
                        'k' => '颜色',
                        'k_id' => 1,
                        'v' => '黑色',
                        'v_id' => 12,
                    ],
                    [
                        'k' => "内存",
                        'k_id' => 2,
                        'v' => '128G',
                        'v_id' => 21,
                    ],
                ]
            ],
        ];
        dd($skus);
        // dd($types);
    }

    public function formatSpecAndItem($specItemIds, $specItemDisplay)
    {
        $result = [];
        $array = explode('_', $specItemIds);
        $items = explode('_', $specItemDisplay);
        foreach ($array as $key => $value) {
            $id = explode('-', $value);
            $item = explode('-', $items[$key]);
            $result[$key]['k_id'] = $id[0];
            $result[$key]['k'] = $item[0];
            $result[$key]['v_id'] = $id[1];
            $result[$key]['v'] = $item[1];
        }
        return $result;
    }


    /**
     * 需要登录的接口
     *
     */
    public function test2()
    {
        $this->success('返回成功', ['action' => 'test2']);
    }

    /**
     * 需要登录且需要验证有相应组的权限
     *
     */
    public function test3()
    {
        $this->success('返回成功', ['action' => 'test3']);
    }
}
