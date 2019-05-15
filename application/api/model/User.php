<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/10
 * Time: 21:46
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address()
    {
        return $this->hasOne('user_address','user_id','id');
    }
    public static function getByOpenID($openid)
    {
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }
}