<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/24
 * Time: 22:32
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'head_img_id', 'topic_img_id'];

    //一对多的关系有hasOne()方法和belongsTo()两个方法
    //当通过有外键的一方关联另一方，用belongsTo()方法
    //当通过没有外键一方关联有外键一方，用hasOne()方法
    public function topicImg(){
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg(){
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    //多对多的关系用belongsToMany
    //参数1：要关联的表模型   参数2:关联两者的中间表表名  参数3：中间表关联目标表的外键  参数4： 本表在中间表的键
    public function product(){
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }

    public static function getThemeByIDs($ids){
        //'topicImg,headImg'之间以逗号隔开，不能有空格
        $result = self::with('topicImg,headImg')->select($ids);
        return $result;
    }

    public static function getThemeWithProduct($id){

        $result = self::with('product,headImg,topicImg')->find($id);
        return $result;
    }
}