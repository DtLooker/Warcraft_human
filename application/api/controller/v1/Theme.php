<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/24
 * Time: 22:32
 */

namespace app\api\controller\v1;


use app\api\validate\IDCollection;

class Theme
{
    public function getSimpleList($ids=''){
        (new IDCollection())->goCheck();

        return 'success';
    }
}