<?php

namespace app\model;

use think\Model;
use think\Session;

class GoodsSku extends Model
{
    protected $table = 'goods_skus';

    // protected $autoWriteTimestamp = 'int';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

}
