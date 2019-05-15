<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/4/14
 * Time: 16:31
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id','delete_time','product_id'];

    public function imgUrl()
    {
        return $this->hasOne('Image','id','img_id');
    }
}