<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/23
 * Time: 22:47
 */

namespace app\api\model;


use think\Model;

class Image extends Model
{
    protected $hidden = ['delete_time', 'update_time', 'from', 'id'];

    //读取器，get(获取的字段名称)Attr,以驼峰形式命名
    //$value 要获取的属性， $data所有字段信息
    public function getUrlAttr($value, $data){
        $finalUrl = $value;
        //1来自本地， 2来自网络
        if($data['from'] == 1){
            $finalUrl = config('setting.url_prefix') . $value;
        }
        return $finalUrl;
    }
}