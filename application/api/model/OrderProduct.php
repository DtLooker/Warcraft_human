<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/2
 * Time: 20:45
 */

namespace app\api\model;


class OrderProduct extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];
}