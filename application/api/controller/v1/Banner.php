<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/19
 * Time: 22:01
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;

class Banner
{
    /**
     * @url   banner:id
     * @http  GET
     * @id   banner模块对应的id号
     */
    public function getBanner($id){

        $data = [
            'id' => $id
        ];

        $validate = new IDMustBePositiveInt();
        //批量验证需要加入batch()
        $result = $validate->batch()->check($data);
        if($result){

        }else{

        }

    }
}