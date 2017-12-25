<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 22:47
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = [
        'delete_time', 'update_time'
    ];

    public function img(){
        $result = $this->belongsTo('Image', 'topic_img_id', 'id');
        return $result;
    }
}