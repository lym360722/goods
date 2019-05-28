<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/6
 * Time: 23:45
 */

namespace app\api\service;

use app\admin\logic\Config;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Log;
use think\Loader;

// 引入微信支付类
// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;
    public function __construct($orderID)
    {
        if(!$orderID){
         throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }
    public function pay()
    {
        // 检测订单号是否存在
        // 检测用户和订单号是否匹配
        // 检测订单是否支付过
        $this->checkOrderValid();


        // 进行库存量检测
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);// 传入总价格
    }

    // 构建微信支付订单信息
    private function makeWxPreOrder($totalPrice)
    {
        //openID
        $openID = Token::getCurrentTokenVar('openid');//去缓存里面获取openID
        if(!$openID){
          throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);//订单号
        $wxOrderData->SetTrade_type('JSAPI');// 交易类型
        $wxOrderData->SetTotal_fee($totalPrice*100);// 交易总金额 微信以分为单位 要*100
        $wxOrderData->SetBody('零食商贩'); // 商品描述
        $wxOrderData->SetOpenid($openID); // 用户身份标识 用户的openID
        $wxOrderData->SetNotify_url(config('secure.pay_back_url')); //异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
        return $this->getPaySignature($wxOrderData);
    }

    //向微信请求预订单号并生成签名
    private function getPaySignature($wxOrderData)
    {
        $wxPayConfig = new WxPayConfig();
        var_dump($wxPayConfig->GetAppId());
        $wxOrder = \WxPayApi::unifiedOrder($wxPayConfig,$wxOrderData);

        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取与支付订单失败','error');
        }
        $this->recodePreOrder($wxOrder);
        $signatrue = $this->sign($wxOrder);

        return $signatrue;
    }

    // 生成签名
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->GetTimeStamp((string)time());

        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->GetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paysign'] = $sign;

        unset($rawValues['appId']);

        return $rawValues;
    }

    // 更新Order表prepay_id
    private function recodePreOrder($wxOrder)
    {
        OrderModel::where('id','=', $this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    // 检测订单号是否存在
    // 检测用户和订单号是否匹配
    // 检测订单是否支付过
    private function checkOrderValid()
    {
        // 检测订单号是否存在
        $order = OrderModel::where('id', '=', $this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }

        // 检测用户和订单号是否匹配
        if(!Token::isValidOprate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }

        // 检测订单是否支付过
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单已支付过了',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }
}