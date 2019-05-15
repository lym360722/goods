<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 17:02
 */

// 验证器基类

namespace app\api\validate;

use app\lib\exception\ParameterException;
use app\lib\exception\parametException;
use mindplay\annotations\standard\VarAnnotation;
use think\Exception;
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
    //自定义验证方法 验证是否正整数
    protected  function isPositiveInteger($value,$rule = '',$data = '',$field = '')
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }else{
           // return $field.'必须是正整数';
            return false;
        }
    }
    //自定义验证方法 验证是否为空
    protected function isNotEmpty($value,$rule = '',$data = '',$field = '')
    {
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }
    // 自定义验证方法 手机号验证
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    public function getDataByRule($arrays)
    {
        if(array_key_exists('user_id',$arrays)|array_key_exists('uid',$arrays)){
            // 不允许包含user_id或者uid，防止恶意覆盖user_id
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}