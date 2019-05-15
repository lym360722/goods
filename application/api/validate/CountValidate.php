<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 21:57
 */

namespace app\api\validate;


class CountValidate extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];
}