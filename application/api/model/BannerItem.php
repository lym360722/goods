<?php

namespace app\api\model;


class BannerItem extends BaseModel
{
    // 隐藏客户端用不上的字段
    protected $hidden = ['delete_time','id','img_id','update_time','banner_id'];
    public function img()
    {
        return $this->hasOne('Image','id','img_id');
    }
}
