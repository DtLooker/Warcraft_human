<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/24
 * Time: 22:32
 */

namespace app\api\controller\v1;

use app\api\model\Theme as ThemeModel;
use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    /***
     *
     * @url \theme?ids=1,2,3...
     * @param string $ids
     * @return false|\PDOStatement|string|\think\Collection
     * @throws ThemeException
     */
    public function getSimpleList($ids=''){
        (new IDCollection())->goCheck();

        $result = ThemeModel::getThemeByIDs($ids);
        if($result->isEmpty()){
            throw new ThemeException();
        }
        return $result;
    }

    /***
     *  @url \theme\:id
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws ThemeException
     */
    public function getComplexList($id){
        (new IDMustBePositiveInt())->goCheck();

        $result = ThemeModel::getThemeWithProduct($id);
        //虽然返回的是数据集，但是查询是通过find()方法，所以不是一个集合，
        //只是单个数据，所以没有isEmpty方法
        if(!$result){
            throw new ThemeException();
        }
        return $result;

    }
}