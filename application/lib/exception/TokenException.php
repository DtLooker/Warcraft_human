<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 23:56
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token过期或者Token无效';
    public $errorCode = 10001;
}