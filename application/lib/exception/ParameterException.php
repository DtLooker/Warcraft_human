<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/21
 * Time: 20:59
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;

}