<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 20:56
 */

namespace app\api\model;


use think\Db;
use think\Model;

class Banner extends Model
{
    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }
    //protected $table = 'category'; 可以修改Banner模型表为category表
//    public static function getBannerByID($id)
//    {
//        //$result = Db::query('select * from banner_item where banner_id=?',[$id]);
//        $result = Db::table('banner_item')
//            //->fetchSql() //可以查看原生SQL语句
//            ->where(['banner_id' => $id])->select();
//        return $result;
//    }
}