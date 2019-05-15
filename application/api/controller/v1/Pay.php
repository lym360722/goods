<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/6
 * Time: 23:35
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IdIntegerValidate;
use app\api\service\Pay as ServicePay;
class Pay extends BaseController
{
    // 前置操作 权限控制 只能用户访问
    protected $beforeActionList = [
        // 下面的方法封装在BaseController基类
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    public function getPreOrder($id='')
    {
        (new IdIntegerValidate())->goCheck();
        $pay = new ServicePay($id);
        return $pay->pay();
    }

    // 回调通知
    public function receiveNotify()
    {
        // 通知频率为15/15/30/180/1800/1800/1800/3600 单位：秒

        // 1. 检测库存量，超卖
        // 2. 更新这个订单的status状态
        // 3. 减库存
        // 4. 如果成功处理，我们返回成功处理的信息，否则，我们需要返回没有成功处理
        // 特点：post；xml格式；

    }
}