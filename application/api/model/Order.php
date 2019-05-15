<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/4
 * Time: 21:50
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;
}