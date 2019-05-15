<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 22:46
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定的类目不存在，请检查参数！';
    public $errorCode = 50000;
}