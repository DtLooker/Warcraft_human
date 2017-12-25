<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 22:47
 */

namespace app\api\controller\v1;


use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategory(){

        $result = CategoryModel::with('img')->select();
        if($result->isEmpty()){
            throw new CategoryException();
        }
        return $result;
    }
}