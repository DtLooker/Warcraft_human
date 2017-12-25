<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 21:53
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg = '查找的商品不存在，请检查参数';
    public $errorCode = 20000;
}