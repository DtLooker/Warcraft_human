<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/4
 * Time: 23:00
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, 'Api.php');

class WxNotify extends \WxPayNotify
{

    /**     微信支付返回XML
     * <xml>
     * <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
     * <attach><![CDATA[支付测试]]></attach>
     * <bank_type><![CDATA[CFT]]></bank_type>
     * <fee_type><![CDATA[CNY]]></fee_type>
     * <is_subscribe><![CDATA[Y]]></is_subscribe>
     * <mch_id><![CDATA[10000100]]></mch_id>
     * <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
     * <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
     * <out_trade_no><![CDATA[1409811653]]></out_trade_no>
     * <result_code><![CDATA[SUCCESS]]></result_code>
     * <return_code><![CDATA[SUCCESS]]></return_code>
     * <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
     * <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
     * <time_end><![CDATA[20140903131540]]></time_end>
     * <total_fee>1</total_fee>
     * <coupon_fee><![CDATA[10]]></coupon_fee>
     * <coupon_count><![CDATA[1]]></coupon_count>
     * <coupon_type><![CDATA[CASH]]></coupon_type>
     * <coupon_id><![CDATA[10000]]></coupon_id>
     * <coupon_fee><![CDATA[100]]></coupon_fee>
     * <trade_type><![CDATA[JSAPI]]></trade_type>
     * <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
     * </xml>
     */
    public function NotifyProcess($data, &$msg)
    {
        if($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];

            try{
                $order = OrderModel::where('order_no', '=', $orderNo)
                    ->find();
                if($order->status == 1){//未支付，才处理
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if($stockStatus['pass']){//检查库存量
                        $this->updateOrderStatus($order->id, true);//更改数据库状态
                        $this->reduceStock($stockStatus);
                    }else{
                        $this->updateOrderStatus($order->id, false);
                    }

                }
            }catch (Exception $e){
                Log::record($e, 'error');
                return false;
            }
        }else{
            return true;//这个返回只是终止微信不停调用回调接口
        }
    }

    //减去卖掉的  更新库存
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus){
            Product::where('id','=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    //更新订单状态
    private function updateOrderStatus($orderID, $success){
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }
}