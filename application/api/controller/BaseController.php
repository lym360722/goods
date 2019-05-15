<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/22
 * Time: 21:42
 */

namespace app\api\controller;

use app\api\service\Token as TokenService;
use think\Controller;

class BaseController extends Controller
{
    public function checkPrimaryScope()
    {
        // 需要用户和管理员都可以访问的权限
        TokenService::needPrimaryScope();
    }

    public function checkExclusiveScope()
    {
        // 只有用户才能访问的接口权限
        TokenService::needExclusiveScope();
    }
}