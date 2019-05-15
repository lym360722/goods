<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 21:54
 */

namespace app\api\controller\v1;


use app\api\validate\CountValidate;
use app\api\model\Product as ProductModel;
use app\api\validate\IdIntegerValidate;
use app\lib\exception\ProdunctException;

class Product
{
    public function getRecent($count = 15)
    {
        (new CountValidate())->goCheck();
        $products = ProductModel::getMostRecent($count);

        if($products->isEmpty()){
            throw new ProdunctException();
        }

        // 数据集collection()零时隐藏属性 可以再dadabase里面配置默认数据集'resultset_type'  => 'collection',
        //$collection = collection($product);
        $products = $products->hidden(['summary']);

        return $products;
    }
    public function getAIICategory($id)
    {
        (new IdIntegerValidate())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProdunctException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getOne($id)
    {
        (new IdIntegerValidate())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProdunctException();
        }
        return $product;
    }
}