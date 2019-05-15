<?php

namespace app\api\model;


class Image extends BaseModel
{
    // 隐藏客户端用不上的字段
    protected $hidden = ['delete_time','update_time','id','from'];

    /**
     * 读取器补全URL前缀
     */
    public function getUrlAttr($value,$data)
    {
        return $this->prefixImgUrl($value,$data);
    }
}
