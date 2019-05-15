<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{

    /**
     * @ 获取器 获取img URL
     * @param $value url字段
     * @param $data  数据表的全部字段
     * @return string 返回图片路径
     */
    protected function prefixImgUrl($value,$data)
    {
        $bannerUrl = $value;

        if ($data['from'] == 1){
            // 在extra目录下自定义一个config配置文件，框架自动会读取extra目录
            $bannerUrl = config('setting.img_prefix').$bannerUrl;//拼接图片完整路径
        }
        return  $bannerUrl;
    }
}
