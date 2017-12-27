<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 23:41
 */

namespace app\api\service;


use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    /**
     * 生成token
     * @return string
     */
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

    public static function getCurrentTokenVar($key){
        //token要通过header传入
        $token = Request::instance()
            ->header('token');

        $vars = Cache::get($token);
        if(!$vars){
            throw new TokenException();

        }else{
            if(!is_array($vars)){
                $vars = json_decode($vars, true);
            }
            if(array_key_exists($key, $vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取Token变量不存在');
            }
        }
    }

    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }
}