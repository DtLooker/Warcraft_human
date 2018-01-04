<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/3
 * Time: 21:54
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    public function getPreOrder($id = ''){

        (new IDMustBePositiveInt())->goCheck();

        //进行支付时候还要进行库存量检测，因为客户不一定下订到后就立马支付。期间库存量是可能变化的
        $pay = new PayService($id);
        $result = $pay->pay();
        return $result;
    }

    public function receiveNotify(){

        //通知频率 15/15/30/180/1800/1800/1800/1800/3600,单位：秒

        //1. 检测库存量
        //2. 更新订单状态
        //3. 减库存
        //如果成功处理，返回微信成功处理信息。 否则，需要返回没有成功处理

        //特点： post: xml格式  不会携带参数
    }
}