<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/27
 * Time: 21:11
 */

namespace app\api\model;


use think\Model;

class ProductImage extends Model
{
    protected $hidden = ['delete_time', 'product_id', 'img_id'];

    //一对一关联
    public function imgUrl(){
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}