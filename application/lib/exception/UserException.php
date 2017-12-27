<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/27
 * Time: 23:46
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 401;
    public $msg = '指定用户不存在';
    public $errorCode = 60000;
}