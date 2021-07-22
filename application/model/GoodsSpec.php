<?php

namespace app\model;

use think\Model;
use think\Session;

class GoodsSpec extends Model
{
    protected $table = 'goods_specs';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

}
