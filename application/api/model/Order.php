<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/5/4
 * Time: 21:50
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    /**
     * 订单详情  读取器   SnapItems字段
     * @param $value
     * @return mixed|null
     */
    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }
    /**
     * 订单详情  读取器 SnapAddress字段
     * @param $value
     * @return mixed|null
     */
    public function getSnapAddressAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public static function getSummaryByUser($uid, $page=1, $size=15)
    {
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size,false,['page' => $page]);
        return $pagingData;
    }
}