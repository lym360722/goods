<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/24
 * Time: 22:47
 */

namespace app\api\model;


class Theme extends BaseModel
{
    // 隐藏客户端用不上的字段
    protected $hidden = ['delete_time','update_time','topic_img_id','head_img_id'];

    public function topicImg()
    {
        return $this->hasOne('Image','id','topic_img_id');
    }

    public function headImg()
    {
        return $this->hasOne('Image','id','head_img_id');
    }

    public function products()
    {
        return $this->belongsToMany('Product','theme_product','product_id',
            'theme_id');
    }

    public static function getThemeBYID($ids){
        $result = self::with('topicImg,headImg')->select($ids);
        return $result;
    }

    public static function getThemeWithProducts($id)
    {
        $themes = self::with('products,topicImg,headImg')
            ->find($id);
        return $themes;
    }
}