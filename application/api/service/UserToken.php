<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 20:22
 */

namespace app\api\service;


use app\lib\enum\EnumScope;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;



class UserToken extends Token
{
    protected $code;
    protected $appId;
    protected $secret;
    protected $wxLoginUrl;

    public function __construct($code)
    {
        $this->code = $code;
        $this->appId = config('wx.appId');
        $this->secret = config('wx.appSecret');
        //sprintf把格式化的字符串写入变量
        $this->wxLoginUrl = sprintf(config('wx.loginUrl'), $this->appId, $this->secret, $this->code);

    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        //json_decode()接受一个json格式字符串，并把它转换成PHP变量
        //assoc 为true时，将返回array； false返回object
        $wxResult = json_decode($result, true);

        if(!$wxResult){
            //没必要返回到客户端的异常，直接用thinkPHP的异常
            throw new Exception('获取session_key及openID异常，微信内部异常');
        }else{
            //调用微信接口，错误会返回一个errcode. 因此需要判断
            $loginFail = array_key_exists('errcode', $wxResult);

            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }
    }

    public function grantToken($wxResult){

        //1. 拿到openId
        $openid = $wxResult['openid'];
        //2. 数据库确认，是否存在此openId
        $user = UserModel::getByOpenID($openid);
        //3. 存在，不处理； 不存在，数据库新增一条数据
        if($user){
            $uid= $user->id;
        }else{
           $uid = $this->newUser($openid);
        }
        //4. 生成令牌，准备缓存数据，写入缓存数据
        //4.1 准备缓存数据
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        //4.2 写入缓存，生成令牌
        $token = $this->saveToCache($cachedValue);

        //把令牌返回到客户端去
        return $token;
    }

    public function saveToCache($cachedValue){
        $key = self::generateToken();
        //转换成字符串
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');

        $request = cache($key, $value, $expire_in);
        if(!$request){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    public function prepareCachedValue($wxResult, $uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //scope=16 代表APP用户权限数值
        //scope=32 代表CMS（管理员权限）数值
        $cachedValue['scope'] = EnumScope::User;

        return $cachedValue;
    }

    public function newUser($openid){
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    public function processLoginError($wxResult){

        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}