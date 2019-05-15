<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/13
 * Time: 13:50
 */

namespace app\api\service;

use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;
use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;

class Token
{
    public static function generateToken()
    {
        // 32个字符组成的随机字符串
        $randChars = getRandChars(32);//公共文件common.php里的方法直接调用

        //用三组字符串进行MD5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];//当前访问的时间戳

        //salt 盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    /**
     * 通用获取缓存的方法，方便多次调用。
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()
            ->header('token');//token通过header头传过来

        $vars = Cache::get($token);//查询缓存里面是否有token

        if(!$vars){
            throw new TokenException();
        }else{
            if(!is_array($vars)){//判断是否是数组
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取Token变量不存在！');
            }
        }
    }

    /**
     * 获取当前用户的Uid
     */
    public static function getCurrentUid()
    {
        // token
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }


    /**
     * 需要用户和管理员都可以访问的权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){//如果缓存内没有过期并且有数据存在
            if($scope >= ScopeEnum::User){//判断scope权限
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }

    }
    /**
     * 只有用户才能访问的接口权限
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');//获取缓存内的scope判断权限
        if($scope){//如果缓存内没有过期并且有数据存在
            if($scope == ScopeEnum::User){//判断scope权限
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }

    }

    /**
     * @ 检查传入的ID是否是当前用户的ID
     * @param $checkedUID
     * @return bool
     * @throws Exception
     * @throws \app\lib\exception\ParameterException
     */
    public static function isValidOprate($checkedUID)
    {
        if(!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检查UID');
        }
        $currretOperateUID = self::getCurrentUid();// 获取当前用户的Uid
        if($currretOperateUID == $checkedUID){
            return true;
        }
        return false;
    }
}