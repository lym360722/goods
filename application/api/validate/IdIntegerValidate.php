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
    protected $message = [
        'id' => 'id必须是正整数'
    ];
}