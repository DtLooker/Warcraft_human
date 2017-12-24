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
    //一对多的关系有hasOne()方法和belongsTo()两个方法
    //当通过有外键的一方关联另一方，用belongsTo()方法
    //当通过没有外键一方关联有外键一方，用hasOne()方法
    public function topicImg(){
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg(){
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }
}