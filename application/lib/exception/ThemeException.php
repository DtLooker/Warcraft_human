<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 19:57
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code = 404;
    public $msg = '指定的主题不存在';
    public $errorCode = 30000;
}