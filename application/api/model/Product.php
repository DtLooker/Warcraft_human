<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/24
 * Time: 22:32
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'create_time',
         'category_id', 'img_id', 'pivot', 'from'];

    //读取器，获取main_img_url
    public function getMainImgUrlAttr($value, $data){
        return $this->prefixImageUrl($value, $data);
    }

    /**
     * 获取最近新品
     * @param $count
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getMostRecent($count){
        $result = self::limit($count)->order('create_time desc')->select();
        return $result;
    }

    /**
     * 获取分类下面所有商品
     * @param $categoryId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getProductsByCategoryID($categoryId){
        $result = self::where('category_id', '=', $categoryId)->select();
        return $result;
    }

}