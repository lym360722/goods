<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 22:35
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time','update_time','create_time'];
    public function img()
    {
        return $this->hasOne('Image','id','topic_img_id');
    }
}