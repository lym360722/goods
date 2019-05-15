<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/24
 * Time: 22:42
 */

namespace app\api\validate;


use app\lib\exception\parametException;

class OrderPlaceValidate extends BaseValidate
{
    protected $rule = [
        'products' => 'checkProducts'
    ];

    //自定义验证数组内值
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count'      => 'require|isPositiveInteger'
    ];
    protected function checkProducts($values)
    {

        if(!is_array($values)){
            throw new parametException([
                'msg' => '商品参数不正确'
            ]);
        }

        if(empty($values)){
            throw new parametException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach ($values as $value){
            $this->checkProduct($value);
        }
        return true;
    }
    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new parametException([
                'msg' => '商品列表参数错误'
            ]);
        }
    }
}