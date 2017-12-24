<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 20:39
 */

namespace app\api\model;


use think\Exception;

class Banner extends BaseModel

{

    protected $hidden = ['delete_time', 'update_time'];

    //model都有一个属性，默认对应此类的表。修改后操作的就是category表了
    // protected $table = 'category';

    //关联表 一对多hasMany
    //参数1：要关联的表的模型名；  参数2：此表关联其他表的外键   参数3：当前表的主键
    public function items()
    {
        return $this->hasMany('bannerItem', 'banner_id', 'id');
    }


    /**
     * 根据id获取具体的banner
     *
     * @param $id
     * @return string
     * @throws Exception
     */
    public static function getBannerById($id)
    {

        //模型查询，返回的是一个模型对象
        //调用with方法，才能获取到关联表的数据。 参数是模型类中关联的方法名，有多个关联参数用数组表示
        $banner = self::with(['items', 'items.img'])->find($id);
        return $banner;
    }
}