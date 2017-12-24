<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/20
 * Time: 20:29
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck(){
        //获取所有request实例
        $request = Request::instance();
        //获取所有参数
        $params = $request->param();
        //对参数进行校验
        $result = $this->batch()->check($params);

        if(!$result){
            $e = new ParameterException([
                'msg' => $this->error
            ]);
            throw $e;
        }else{
            return true;
        }
    }

    //验证是否为正整数
    protected function isPositiveInteger($value, $rule='', $data='',$filed=''){
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }else{
            return false;
        }
    }
}