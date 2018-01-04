<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 23:41
 */

namespace app\api\service;


use app\lib\enum\EnumScope;
use app\lib\exception\ForbiddenException;
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

    //获取获取存储在缓存中的任意参数
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
    //获取当前uid
    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    //app用户和CMS管理员才具备的权限
    public static function needPrimaryScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= EnumScope::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    // app用户才具备的权限
    public static function needExclusiveScope(){
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == EnumScope::User){
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    //检查是否是当前用户
    public static function isValidateOperate($checkedUID){
        if(!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            return true;
        }
        return false;
    }
}