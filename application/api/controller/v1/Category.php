<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/29
 * Time: 22:34
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategory()
    {
        $category  = CategoryModel::all([],'img');
        
        if($category->isEmpty()){
            throw new CategoryException();
        }
        return $category;
    }
}