<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/19
 * Time: 22:52
 */

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger',
    ];

    protected $message = [
        'id' => 'id必须是正整数'
    ];

}