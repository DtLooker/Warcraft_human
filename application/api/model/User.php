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

    //一对一关联   没有外键的一方  关联有外键的一方
    public function address(){
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    /**
     * 通过openid 获取用户
     * @param $openid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getByOpenID($openid){
        $user = self::where('openid','=', $openid)->find();
        return $user;
    }

}