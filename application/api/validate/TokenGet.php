<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/10
 * Time: 21:37
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
      'code' => 'require|isNotEmpty'
    ];
    protected $message = [
        'code' => 'code不能为空！'
    ];
}