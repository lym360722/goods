<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/12
 * Time: 20:23
 */

namespace app\api\service;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'api.php');
Loader::import('WxPay.WxPay',EXTEND_PATH,'Notify.php');
class WxNotify extends \WxPayNotify
{
    /*
       <xml>
           <return_code><![CDATA[SUCCESS]]></return_code> 返回状态码
           <return_msg><![CDATA[OK]]></return_msg>  返回信息
           <appid><![CDATA[wx2421b1c4370ec43b]]></appid>  小程序ID
           <mch_id><![CDATA[10000100]]></mch_id>  商户号
           <nonce_str><![CDATA[IITRi8Iabbblz1Jc]]></nonce_str> 随机字符串
           <openid><![CDATA[oUpF8uMuAJO_M2pxb1Q9zNjWeS6o]]></openid>
           <sign><![CDATA[7921E432F65EB8ED0CE9755F0E86D72F]]></sign> 签名
           <result_code><![CDATA[SUCCESS]]></result_code> 业务结果
           <prepay_id><![CDATA[wx201411101639507cbf6ffd8b0779950874]]></prepay_id> 预支付交易会话标识
           <trade_type><![CDATA[JSAPI]]></trade_type>  交易类型
        </xml>
    */
    public function NotifyProcess($objData, $config, &$msg)
    {
        if($objData['resilt_code'] == 'success'){ // 判断支付结果
            $orderNo = $objData['out_trade_no'];
            Db::startTrans(); // 加事务防止并发多次提交 事务锁   锁的概念
            try{
                $order = OrderModel::where('order_no','=',$orderNo)
                    ->find();
                if ($order->status == 1){
                    $service = new OrderService();
                    $stock_status  = $service->checkOrderStock($order->id);// 检查库存量
                    if($stock_status['pass']){
                        $this->updateOrderStatus($order->id,true); // 更新订单状态已支付
                        $this->reduceStock($stock_status); // 减库存
                    }else{
                        $this->updateOrderStatus($order->id,false); // 更新订单为已支付，但是库存不足
                    }
                }
                Db::commit(); // 提交事务
                return true;
            }catch (\Exception $e){
                Db::rollback(); // 回滚事务
                Log::error($e);
                return false;
            }
        }else{
            return true;
        }
    }
    // 根据支付结果修改订单status字段的状态
    private function updateOrderStatus($orderID,$success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)->update(['status'=>$status]);
    }

    // 减库存
    private function reduceStock($stockStatus)
    {
        foreach($stockStatus['pStatusArray'] as $singlePStatus)
        {
            // $singlePStatus['count']
            Product::where('id', '=', $singlePStatus['id'])->setDec('stock',$singlePStatus['count']);
        }
    }
}