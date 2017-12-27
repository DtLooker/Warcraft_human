<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/27
 * Time: 21:12
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'product_id'];
}