<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/13
 * Time: 14:36
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code =401;
    public $msg  = 'Token已过期或无效Token';
    public $errorCode = 10001;
}