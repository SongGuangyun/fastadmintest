<?php

namespace app\model;

use think\Model;
use think\Session;

class GoodsSpecItem extends Model
{
    protected $table = 'goods_spec_items';


    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

}
