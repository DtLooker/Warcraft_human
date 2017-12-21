<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 20:39
 */

namespace app\api\model;


use think\Db;
use think\Exception;
use think\Model;

class Banner extends Model
{
    /**
     * 根据id获取具体的banner
     *
     * @param $id
     * @return string
     * @throws Exception
     */
    public static function getBannerById($id)
    {
        $result = Db::table('banner_item')->where('banner_id', '=', $id)->select();
        return $result;
    }
}