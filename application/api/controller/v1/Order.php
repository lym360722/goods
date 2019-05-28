<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/21
 * Time: 21:26
 */

namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\validate\IdIntegerValidate;
use app\api\validate\OrderPlaceValidate;
use app\api\validate\PagingParameter;
use app\api\service\Token as TokenService;
use app\api\Model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\exception\OrderException;
use FontLib\Table\Type\post;


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



    // 前置操作 权限控制 用户和管理员访问控制
    protected $beforeActionList = [
        // 下面的方法封装在BaseController基类
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope'   => ['only' => 'getDetail,getSummaryByUser']
    ];



    /**
     * 我的订单
     * @url 'api/:version/order/by_user?page=1&size=15'
     * @method GET
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\parametException
     */
    public function getSummaryByUser($page=1,$size=15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size); // 返回Object
        if($pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $pagingOrders->getCurrentPage()
            ];
        }
        $data = $pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toarray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders->getCurrentPage()
        ];
    }



    /**
     * 订单详情
     * @url 'api/:version/order/:id'
     * @method GET
     * @param $id
     * @return OrderModel
     * @throws OrderException
     * @throws \app\lib\exception\parametException
     * @throws \think\exception\DbException
     */
    public function getDetail($id)
    {
        (new IdIntegerValidate())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    /**
     * 下单
     * @url 'api/:version/order'
     * @method POST
     * @return array
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\parametException
     * @throws \think\Exception
     */
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