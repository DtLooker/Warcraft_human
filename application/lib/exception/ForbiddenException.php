<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/28
 * Time: 19:54
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;

}