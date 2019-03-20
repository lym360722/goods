<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 23:12
 */

namespace app\lib\exception;

use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    public function render(\Exception $e)
    {
        //instanceof 作用：（1）判断一个对象是否是某个类的实例，（2）判断一个对象是否实现了某个接口。
        // 判断异常是否是自定义异常
        if($e instanceof BaseException){
            //如果是自定义的异常
            $this->code      = $e->code;
            $this->msg       = $e->msg;
            $this->errorCode = $e->errorCode;
        }else{
            // Config::get('app_debug'); 调用Config中的app_debug来做开关
            if(config('app_debug')){
                // 如果debug开启就执行框架中的方法，否则就写入日志
                return parent::render($e);
            }else{
                $this->code      = 500;
                $this->msg       = '服务器内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);//调用自定义生成日志方法
            }
        }
        $request = Request::instance();
        $result = [
            'msg'         => $this->msg,
            'error_code'  => $this->errorCode,
            'request_url' => $request->url()//获取当前请求的路径
        ];
        return json($result,$this->code);//返回到客户端的普通异常
    }

    /**
     * 自定义生成日志
     * @param \Exception $e
     */
    private  function recordErrorLog( \Exception $e)
    {
        Log::init([
            'type'  => 'File',
            'path'  => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}