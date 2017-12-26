<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 22:08
 */

namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code = 404;
    public $msg = '微信接口调用失败';
    public $errorCode = 999;
}