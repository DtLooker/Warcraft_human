<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/25
 * Time: 21:51
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];
}