<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/17
 * Time: 22:42
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code = 201;
    public $msg  = 'ok';
    public $errorCode = 0;
}