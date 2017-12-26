<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 23:41
 */

namespace app\api\service;


class Token
{
    protected static function generateToken(){
        //用三组数字进行md5加密
        //第一组：32个随机字符串
        $randChar = getRandChar(32);
        //时间
        $timestamp = $_SERVER['REQUEST_TIME'];
        // salt
        $salt = config('secure.token_salt');

        return md5($randChar . $timestamp . $salt);

    }
}