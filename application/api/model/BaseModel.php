<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/24
 * Time: 19:35
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    //名字为getUrlAttr时候，会被认为是读取器，就会执行下面的代码。
    //也许在别的模型下面url字段是别的意思， 所以名字不能按读取器（获取器）规则命名
    protected function prefixImageUrl($value, $data){

        $finalUrl = $value;
        //1来自本地， 2来自网络
        if($data['from'] == 1){
            $finalUrl = config('setting.url_prefix') . $value;
        }
        return $finalUrl;
    }
}