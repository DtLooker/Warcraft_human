<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/1
 * Time: 21:24
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查id';
    public $errorCode = 80000;
}