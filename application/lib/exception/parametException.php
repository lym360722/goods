<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/3
 * Time: 15:46
 */

namespace app\lib\exception;


class parametException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;
}