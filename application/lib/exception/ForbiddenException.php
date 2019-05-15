<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/18
 * Time: 22:49
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够！';
    public $errorCode = 100001;
}