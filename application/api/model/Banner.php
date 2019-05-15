<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/2
 * Time: 20:56
 */

namespace app\api\model;


class Banner extends BaseModel
{
    //protected $table = 'category'; 可以修改Banner模型表为category表

    // 隐藏客户端用不上的字段
    protected  $hidden = ['delete_time','update_time'];

    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerByID($id)
    {
        $banner = self::with(['items','items.img'])->find($id); //建议选择静态方式调用
        return $banner;
    }
}