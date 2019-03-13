<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 15:51
 */

// BaseValidate 子类

namespace app\api\validate;


class IdIntegerValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number|isPositiveInteger',
    ];
    //自定义验证方法
    protected  function isPositiveInteger($value,$rule = '',$data = '',$field = '')
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }else{
            return $field.'必须是正整数';
        }
    }

}