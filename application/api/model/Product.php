<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/24
 * Time: 22:46
 */

namespace app\api\model;


class Product extends BaseModel
{
    // 隐藏客户端用不上的字段
    protected $hidden = ['delete_time','update_time','create_time','from',
        'category_id','topic_img_id','pivot','head_img_id'];

    /**
     * 读取器补全URL前缀
     */
    public function getMainImgUrlAttr($value,$data)
    {
        return $this->prefixImgUrl($value,$data);
    }

    public static function getMostRecent($count)
    {
        $products = self::limit($count)
            ->order('create_time desc')
            ->select();
        return $products;
    }

    public static function getProductsByCategoryID($categoryID)
    {
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }

    public function images(){
        return $this->hasMany('ProductImage','product_id','id');
    }

    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }

    public static function getProductDetail($id)
    {
        $productDetail = self::with([
            'images' => function($query){//闭包排序
                $query->with(['imgUrl'])
                    ->order('order','asc');
            }
        ])
            ->with(['properties'])
            ->find($id);
        return $productDetail;
    }
}