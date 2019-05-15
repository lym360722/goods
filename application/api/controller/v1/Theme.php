<?php
/**
 * Created by PhpStorm.
 * User: 刘玉敏
 * Date: 2019/3/24
 * Time: 22:45
 */

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IdIntegerValidate;
use app\lib\exception\ThemeException;

class Theme
{
    /**
     * @ /theme?ids=id1,id2,id3....
     * @ return 一组theme模型
     */
    public function getSimpleList($ids='')
    {
        (new IDCollection())->goCheck();
        $ids = explode(',',$ids);
        $result = ThemeModel::getThemeBYID($ids);
        if($result->isEmpty()){
            throw new ThemeException();
        }
        return $result;
    }

    /**
     * @url /theme/:id
     * @param $id
     */
    public function getComplexOne($id)
    {
        (new IdIntegerValidate())->goCheck();

        $theme = ThemeModel::getThemeWithProducts($id);

        if($theme->isEmpty()){
            throw new ThemeException();
        }
        return $theme;
    }
}