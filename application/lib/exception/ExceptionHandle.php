<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 21:23
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

/**
 * Class ExceptionHandle        自定义全局异常类
 * @package app\lib\exception
 *
 * 重写 Handle，需要在config中配置，传入命名空间
 */
class ExceptionHandle extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //需要返回客户端当前请求URL

    //所有异常都要经过render渲染
    public function render(Exception $e)
    {
        // 需要客户端知道的异常,自定义异常
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;

        } else {
            //调试模式时候，打印出错误信息
            if(config('app_debug')){
                return parent::render($e);

            }else{
                $this->code = 500;
                $this->msg = '服务器内部异常，无需知道';
                $this->errorCode = 999;
                $this->recodeErrorLog($e);
            }
        }

        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'errCode' => $this->errorCode,
            'url' => $request->url()
        ];

        return json($result, $this->code);
    }

    public function recodeErrorLog(Exception $e){
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);

        Log::record($e->getMessage(), 'error');
    }
}