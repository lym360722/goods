<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/2/28
 * Time: 22:28
 */

namespace app\api\controller\v1;


use app\api\validate\IdIntegerValidate;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;


class Banner
{
    /**
     * 获取指定ID的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的ID号
     *
     */
    public function getBanner($id)
    {
        // AOP 面向切面编程
        (new IdIntegerValidate())->goCheck();

        $banner = BannerModel::getBannerByID($id);

        if(!$banner){
            throw new BannerMissException();
        }
        return $banner;
    }

}