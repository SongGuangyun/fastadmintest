<?php

namespace app\model;

use think\Model;
use think\Session;

class Goods extends Model
{
    protected $table = 'goods';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

}
