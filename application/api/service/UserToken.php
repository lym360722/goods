<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/10
 * Time: 21:48
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ThemeException;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    /**
     * 构造器拼接获取Token的URL
     * UserToken constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->code         = $code;
        $this->wxAppID      = config('wx.app_id');
        $this->wxAppSecret  = config('wx.app_secret');

        // sprintf() 把百分号（%）符号替换成一个作为参数进行传递的变量
        $this->wxLoginUrl   = sprintf(Config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);

    }

    /**
     * 获取Token
     * @curl_get() //向微信服务器发送curl
     * @return mixed
     * @throws Exception
     * @throws WeChatException
     */
    public function get()
    {
        // curl_get()方法在common.php建立公共的方便多次调用
        $result     = curl_get($this->wxLoginUrl);
        //转化成数组
        $wxResult   = json_decode($result,true);
        if(empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误！');
        }else{
            // 判断微信返回的是否有错误码
            $loginFail = array_key_exists('errcode',$wxResult);
            if($loginFail){
                $this->processLoginError($wxResult);//抛出异常
            }else{
                return $this->grantToken($wxResult);
            }
        }
    }

    /**
     * @param $wxResult
     * @return mixed
     * @throws TokenException
     */
    private function grantToken($wxResult)
    {
        // 拿到openID
        $openid = $wxResult['openid'];

        //查询数据库这个openID是否存在
        $user = UserModel::getByOpenID($openid);

        // 如果存在则不处理 如果不存在就新增一条user记录
        if($user){
            $uid = $user->id;
        }else{
            $uid = $this->newUser($openid); // 新增一条数据返回用户ID
        }

        // 生成令牌 准备缓存数据，写入缓存
        //key:令牌
        //value：wxResult，uid，scope
        $cachedValue = $this->perpareCachedValue($wxResult,$uid);//拼装缓存数据
         $token = $this->saveToCahe($cachedValue);
        // 把令牌返回到客户端去
        return $token;
    }

    /**
     * 缓存数据并返回
     * @param $cachedValue
     * @return mixed
     * @throws TokenException
     */
    private function saveToCahe($cachedValue)
    {
        $key = self::generateToken();//获取令牌 调用基类的获取随机字符串的方法
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');// 获取设置缓存的时间 setting.php

        $request = cache($key,$value,$expire_in);
        if(!$request){
            throw new TokenException([
               'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;//返回随机字符串令牌
    }

    /**
     * //拼装缓存数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function perpareCachedValue($wxResult,$uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        // scope = 16 代表App用户的权限值
        $cachedValue['scope'] = ScopeEnum::User;
        //$cachedValue['scope'] = 15;

        //scope = 32 代表CMS的用户权限值
        return $cachedValue;
    }

    /**
     * 新增一条数据并返回新增ID
     * @param $openid
     * @return mixed
     */
    private function newUser($openid)
    {
        $user = UserModel::create([
           'openid' => $openid
        ]);
        return $user->id;
    }

    /**
     * 异常信息
     * @param $wxResult
     * @throws WeChatException
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatException([
            'msg'       => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}