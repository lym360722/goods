<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/2
 * Time: 22:14
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

class Order
{
    // 订单的商品列表，也就是客户端传过来的products参数
    protected  $OProducts;

    // 商品信息（包括库存量）
    protected  $products;

    protected $uid;

    /**
     * @param $uid
     * @param $OProducts
     * @return array
     * @throws Exception
     */
    public function place($uid,$OProducts)
    {
        // 创建订单前查询商品库存
        // Oproducts和products 作对比
        // product从数据库中查询出来
        $this->OProducts = $OProducts;
        $this->products  = $this->getProductsByOrder($OProducts);// 根据订单信息查找商品信息方法
        $this->uid       = $uid;
        $status          = $this->getOrderStatus();//库存量对比方法
        if(!$status['pass']){
            $status['order_id'] = -1;
            return $status;
        }

        // 开始创建订单
        $orderSnap = $this->snapOrder($status);

        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }


    private function createOrder($snap)
    {
        Db::startTrans();
        try {
            // 订单插入数据库 Order
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();

            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            // 插入中间表数据 order_product
            $create_time = $order->create_time;
            $orderID = $order->id;

            foreach ($this->OProducts as &$p) { // 把orderID 插进 客户端穿过来的 oProducts 订单列表数组里面
                $p['order_id'] = $orderID;
            }

            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->OProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'order_time' => $create_time
            ];

        }catch (Exception $e){
            Db::rollback();
            throw $e;
        }
    }

    /**
     *  生成订单号
     *  年 . 月 . 日 . Unix时间戳 . 微秒时间戳 . 0-99随机数
     *  intval() 函数用于获取变量的整数值。
     *  strtoupper() 函数把字符串转换为大写。dechex() 函数把十进制转换为十六进制。
     *  substr() 函数返回字符串的一部分。
     *  sprintf() 函数把格式化的字符串写入变量中。
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = array('A','B','C','D','E','F','G','H','J','K','L','M','N','R','T','U','W','X','Y');

        $orderSn = $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
            'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d',rand(0, 99));
        return $orderSn;
    }

    // 生成订单快照
    private function snapOrder($status)
    {
        $snap = [
          'orderPrice' => 0,
          'totalCount' => 0,
          'pStatus'    => [],
          'snapAddress'=> null,
          'snapName'   => '',
          'snapImg'    => ''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus']    = $status['pStatusArray'];
        $snap['snapAddress']    = json_encode($this->getUserAddress());
        $snap['snapName']       = $this->products[0]['name'];
        $snap['snapImg']        = $this->products[0]['main_img_url'];
        if(count($this->products) > 1){
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
        return $userAddress->toArray();
    }



    /**
     * 支付时检查商品库存
     * @param $orderID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function checkOrderStock($orderID)
    {
        // 根据orderID查询关联表获取商品下单数据
        $oProducts = OrderProduct::where('order_id', '=', $orderID)->select();
        $this->OProducts = $oProducts;

        // 根据订单信息查找商品信息
        $this->products = $this->getProductsByOrder($oProducts);

        //库存量对比
        $status = $this->getOrderStatus();
        return $status;
    }

    // 库存量对比
    private function getOrderStatus()
    {
        $status = [
          'pass'         => true,
          'orderPrice'   => 0,
          'totalCount'   => 0,
          'pStatusArray' => []
        ];
        foreach ($this->OProducts as $OProduct){
            $pStatus = $this->getProductStatus(
                $OProduct['product_id'],$OProduct['count'],$this->products
            );
            if (!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totaPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }

    private function getProductStatus($oPID,$oCount,$products)
    {
        $pIndex = -1;

        $pStatus = [
          'id'         => null,
          'haveStock'  => false,
          'count'      => 0,
          'name'       => '',
          'totaPrice' => 0
        ];

        for ($i=0; $i<count($products); $i++){
            if ($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }
        if($pIndex == -1){
            throw new OrderException([
                'msg' => 'id为'.$oPID.'的商品不存在，创建订单失败'
            ]);
        }else{

            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oCount;
            $pStatus['totaPrice'] = $product['price']*$oCount;

            if (($product['stock'] - $oCount) >= 0){
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    // 根据订单信息查找商品信息
    public function getProductsByOrder($OProducts)
    {
        $oPIDs = [];
        foreach ($OProducts as $item){
            array_push($oPIDs,$item['product_id']);
        }

        $product = Product::all($oPIDs)
            ->visible(['id','price','stock','name','main_img_url'])
            ->toArray();

        return $product;
    }
}