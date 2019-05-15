<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/21
 * Time: 21:26
 */

namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\OrderPlaceValidate;
use app\lib\enum\ScopeEnum;
use app\api\service\Token as TokenService;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\api\service\Order as OrderService;
use FontLib\Table\Type\post;
use think\Request;

class Order extends BaseController
{
    // 用户在选择商品后，向API提交包含它所选择商品的相关信息
    // API在接收到信息后，需要检查订单相关商品的库存量
    // 有库存，把订单数据存入数据库中= 下单成功了，返回客户端信息，告诉客户端可以支付了
    // 调用我们的支付接口，进行支付
    // 还需要再次进行库存量检测
    // 服务器这边就可以调用微信的支付接口进行支付
    // 小程序根据服务器返回的结果拉起微信支付
    // 微信会返回给我们一个支付的结果（异步）
    // 成功：也需要进行库存量的检测
    // 成功：进行库存量的扣除



    // 前置操作 权限控制 只能用户访问
    protected $beforeActionList = [
        // 下面的方法封装在BaseController基类
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    public function placeOrder()
    {

        (new OrderPlaceValidate())->goCheck();

        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();//获取用户的id

        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }


}