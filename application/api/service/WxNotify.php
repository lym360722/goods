<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/12
 * Time: 20:23
 */

namespace app\api\service;
use think\Loader;

Loader::import('WxPay.WxPay',EXTEND_PATH,'api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, &$msg)
    {

    }
}