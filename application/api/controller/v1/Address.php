<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/16
 * Time: 23:27
 */

namespace app\api\controller\v1;


use app\api\validate\AddressNewValidate;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;


class Address extends BaseController
{
    // 前置操作
    protected $beforeActionList = [
        // 下面的方法封装在BaseController基类
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress']
    ];


    public function createOrUpdateAddress()
    {
        $validate = new AddressNewValidate();
        $validate->goCheck();


        // 根据Token获取用户id
        $uid = TokenService::getCurrentUid();



        // 根据用户id查找用户数据，用户不存在抛出异常
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }



        // 获取用户从客户端提交过来的地址信息
        $dataArray = $validate->getDataByRule(input('post.'));



        // 根据用户地址信息是否存在，从而判断是添加地址还是更新地址
        $userAddress = $user->address;
        if(!$userAddress){
            $user->address()->save($dataArray);//模型关联新增 关联方法带括号
        }else{
            $user->address->save($dataArray);// 模型关联更新 关联方法不带括号
        }
        // return $user;
        // return 'success';
        return json(new SuccessMessage(),201);//定义一个返回成功的信息
    }
}