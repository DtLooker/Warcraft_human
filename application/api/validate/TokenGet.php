<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/26
 * Time: 20:15
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    //当post传入时候，值传入参数code 不传入code的值。是可以通过验证的，所以要检查是否为空
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '没有code，无法获取Token'
    ];
}