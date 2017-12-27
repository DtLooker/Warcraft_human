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

    //关联product_image 一对多，一个产品，多个图片
    public function imgs(){
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    //关联product_property  一对多，一个产品，对应多个属性
    public function properties(){
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }

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

    /**
     * 获取对应 id 的商品详情
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getProductDetail($id){

        //$result = self::with(['imgs.imgUrl', 'properties'])->find($id);

        /*对关联的表中某个字段进行排序，按此种方法*/
        $result = self::with([
            'imgs' => function($query){
                $query->with('imgUrl')
                      ->order('order', 'asc');
            }
        ])
            ->with(['properties'])->find($id);

        return $result;
    }



}