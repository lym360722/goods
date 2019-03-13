<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 23:21
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = '请求的Banner不存在';
    public $errorCode = 40000;
}