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

    //验证是否为空
    protected function isNotEmpty($value, $rule='', $data='', $filed=''){
        if(!$value){
            return false;
        }else{
            return true;
        }
    }

    //验证手机号码
    protected function isMobile($value, $rule='', $data='', $field=''){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if($result){
            return true;
        }else{
            return false;
        }
    }

    //获取所有通过规则验证的参数
    public function getDataByRule($arrays){
        //用户id是不能通过用户直接传递过来的，有此情况，判定为非法
        if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)){
            throw  new ParameterException([
                'msg' => '参数中包含非法参数名user_id或uid'
            ]);
        }

        $newArray = [];
        foreach ($this->rule as $key => $value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}