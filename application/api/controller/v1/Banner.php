<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/19
 * Time: 22:01
 */

namespace app\api\controller\v1;


use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;

class Banner
{
    /**
     * @url   banner:id
     * @http  GET
     * @id   banner模块对应的id号
     */
    public function getBanner($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        //Db查询返回一个banner的数组
        $banner = BannerModel::getBannerById($id);


        if(!$banner){
            throw new BannerMissException();
        }
        return $banner;

    }
}