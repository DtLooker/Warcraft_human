<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/23
 * Time: 22:35
 */

namespace app\api\model;


use think\Model;

class BannerItem extends Model
{
    protected $hidden = ['delete_time', 'update_time', 'id', 'img_id', 'banner_id'];

    //一对一的关联用belongsTo
    //参数1：要关联的模型名  参数2：两表之间相关联的外键  参数3：此表对应的主键
    public function img(){
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}