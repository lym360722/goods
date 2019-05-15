<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/10
 * Time: 21:35
 */

namespace app\api\controller\v1;

//---------------------------------token令牌
use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    public function getToken($code = '')
    {
        (new TokenGet())->goCheck();//验证code参数
        $ut     = new UserToken($code);//实例化服务层UserToken传入code参数 * 构造器 *
        $token  = $ut->get();//调用get方法获取Token
        return [
            'token' => $token
        ];
    }
}