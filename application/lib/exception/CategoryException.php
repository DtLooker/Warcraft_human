<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 22:48
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定的分类不存在，请检查ID';
    public $errorCode = 50000;
}