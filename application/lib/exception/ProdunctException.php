<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 22:09
 */

namespace app\lib\exception;


class ProdunctException extends BaseException
{
    public $code = 404;
    public $msg = '指定的商品部存在，请检查参数！';
    public $errorCode = 20000;
}