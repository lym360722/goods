<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 17:02
 */

// 验证器基类

namespace app\api\validate;

use app\lib\exception\parametException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //获取http传入的参数
        //对这些参数做校验
        $request = Request::instance();
        $params  = $request->param();
        $result  = $this->batch()->check($params);
        if(!$result){
            // 调用 parametException 类抛出验证获取http传入的参数的参数异常
            $e = new parametException([
                'msg' => $this->error // 把异常msg 修改成验证器的错误信息
            ]);
            throw $e;
        }else{
            return true;
        }
    }
}