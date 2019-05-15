<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/18
 * Time: 21:29
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    // 隐藏客户端用不上的字段
    protected $hidden = ['delete_time','update_time'];
}