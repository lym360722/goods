<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/4
 * Time: 21:02
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = '用户不存在';
    public $errorCode = 60000;
}