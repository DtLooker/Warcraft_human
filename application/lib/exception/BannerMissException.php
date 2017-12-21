<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 21:28
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    //http状态码
    public $code = 404;
    public $msg = '请求的banner不存在在';
    public $errorCode = 40000;
}