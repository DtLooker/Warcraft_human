<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/28
 * Time: 20:09
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;

class Order extends BaseController
{
    // 用户在选择商品后，向API提交包含它所选择的商品的相关信息
    // API在接收到信息后，需要检查订单相关商品的库存量
    // 有库存，把订单数据存入数据库中 ---> 下单成功，返回客户端消息，告诉客户端可以支付了
    // 调用支付接口，进行支付
    // 此时，好需要再次进行库存量检测
    // 服务器调用微信的支付接口进行支付
    // 小程序根据返回的参数拉起微信支付
    // 微信返回给我们一个支付结果（异步）
    // 成功： 也需要进行库存量检查
    // 成功： 进行库存量的扣除

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    public function placeOrder(){
        (new OrderPlace())->goCheck();

        //获取客户端提交过来的订单信息
        //传递过来的json数据，键是products。对应的值是数组，所以要加上‘/a’；
        $products = input('post.products/a');
        //根据 token 获取缓存里面的uid
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid, $products);

        return $status;
    }
}