<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 22:52
 */

namespace app\api\model;


class User extends BaseModel
{
    public static function getByOpenID($openid){
        $user = self::where('openid','=', $openid)->find();
        return $user;
    }

}