<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/2
 * Time: 20:31
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['delete_time', 'create_time', 'update_time'];

    protected $autoWriteTimestamp = true;
}