<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/16
 * Time: 23:31
 */

namespace app\api\validate;


class AddressNewValidate extends BaseValidate
{
    protected $rule = [
        'name'      => 'require|isNotEmpty' ,
        'mobile'    => 'require|isMobile',
        'province'  => 'require|isNotEmpty',
        'city'      => 'require|isNotEmpty',
        'country'   => 'require|isNotEmpty',
        'detail'    => 'require|isNotEmpty'
    ];
}