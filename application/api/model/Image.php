<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/23
 * Time: 22:47
 */

namespace app\api\model;


class Image extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'from', 'id'];

    //读取器，get(获取的字段名称)Attr,以驼峰形式命名
    //$value 要获取的属性， $data所有字段信息
    public function getUrlAttr($value, $data){
       return $this->prefixImageUrl($value, $data);
    }
}