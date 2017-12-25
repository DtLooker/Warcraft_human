<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 21:46
 */

namespace app\api\controller\v1;


use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\CategoryException;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count=15){
        (new Count())->goCheck();

        //tp5 默认查询返回的是array对象, 可以在database配置里面修改，使其返回对象是数据集
        //collection的好处是可以是指调用其隐藏属性的方法，只是零时隐藏。有一些字段需要零时隐藏
        $result = ProductModel::getMostRecent($count);

        //返回array对象的时候可以用if(!$result)判空，返回数据集后，要用isNotEmpty()方法
        if($result->isEmpty()){
            throw new ProductException();
        }
        $result = $result->hidden(['summary']);
        return $result;
    }


    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();

        $result = ProductModel::getProductsByCategoryID($id);
        if($result->isEmpty()){
            throw new CategoryException();
        }
        $result = $result->hidden(['summary']);
        return $result;
    }
}