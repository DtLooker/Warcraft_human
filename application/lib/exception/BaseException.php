<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 21:25
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    //状态码
    public $code = 400;
    //错误信息
    public $msg = '参数错误';
    //错误码
    public $errorCode = 10000;

    public function __construct($params = [])
    {
        //防御性机制，如果不是数组。直接返回
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('code', $params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg', $params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('errorCode', $params)){
            $this->errorCode = $params['errorCode'];
        }
    }
}