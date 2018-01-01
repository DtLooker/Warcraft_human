<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/29
 * Time: 21:19
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    /**
     * 下单，客户端提供的数据形式大概如下：
     *
     * products [
     *      [
     *          'product_id':1,
     *          'count':2
     *      ],
     *      [
     *          'product_id':2,
     *          'count':3
     *      ],
     *      [
     *          'product_id':3,
     *          'count':5
     *      ]
     *  ]
     */
    protected $rule = [
        'products' => 'checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    public function checkProducts($values){
        if(!is_array($values)){
            throw new ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }
        if(empty($values)){
            throw new ParameterException([
                'msg' => '商品列表不能是空'
            ]);
        }
        foreach ($values as $value){
            $this->checkProduct($value);
        }
    }

    //检查单个商品
    public function checkProduct($value){
        $validate = new BaseValidate($this->singleRule);
        $validate->check($value);
    }
}