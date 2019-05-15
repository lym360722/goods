<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/3
 * Time: 0:09
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 80000;
}