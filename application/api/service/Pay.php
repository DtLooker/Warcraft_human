<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018/1/3
 * Time: 21:59
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use think\Loader;
use think\Log;

//引入没有命名空间的php文件，使用Loader::import()
Loader::import(WxPay . WxPay, EXTEND_PATH, Api . php);

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        //支付前需要对订单进行检测
        //1.订单号根本就不存在
        //2.订单号存在，与对应的用户不匹配
        //3.订单有可能已经被支付
        $this->checkOrderValid();

        //4.进行库存量检查
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    //生成预订单到微信服务器，获取返回参数
    public function makeWxPreOrder($totalPrice)
    {
        //openid
        $openid = TokenService::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOut_trade_no($this->orderNO);//订单号
        $wxOrderData->SetTotal_fee($totalPrice * 100);//交易金额，微信基本单位为分
        $wxOrderData->SetNotify_url('http://qq.com');
        $wxOrderData->SetTrade_type("JSAPI");//交易类型
        $wxOrderData->SetOpenid($openid);

        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        //记录预订单id  即prepay_id,向用户发送模板消息需要prepay_id，先保存到数据库
        $this->recordPreOrder($wxOrder);

        return $this->sign($wxOrder);
    }

    private function sign($wxOrder){
        //预订单从微信服务器获取的参数，再与生成的签名  返回客户端，
        //客户端通过这些参数调起微信支付   支付服务器通过相同算法比对  相同则开启支付
        $jsApiPayData = new \WxPayJsApiPay();

        //设置返回的参数 小程序ID，时间戳，随机串，数据包，签名方式
        //设置小程序ID
        $jsApiPayData->SetAppid(config('wx.appId'));
        //设置时间戳
        $jsApiPayData->SetTimeStamp((string)time());
        //设置随机串
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);

        //设置数据包
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        //设置签名方式
        $jsApiPayData->SetSignType('md5');

        //获取微信返回的处理后的参数组
        $rawValues = $jsApiPayData->GetValues();
        //生成签名
        $sign = $jsApiPayData->MakeSign();
        //将签名添加到返回的参数组中
        $rawValues['paySign'] = $sign;

        //返回元素数据中包含小程序appid，没必要返回到客户端去
        unset($rawValues['appId']);

        return $rawValues;

    }

    //记录预订单
    private function recordPreOrder($wxOrder){
        OrderModel::where('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    //支付前对订单进行非库存检测
    public function checkOrderValid()
    {
        $order = OrderModel::where('id', '=', $this->orderID)
            ->find();

        //1.检测订单号是否存在
        if (!$order) {
            throw new OrderException();
        }

        //2.检测订单号对应的用户是否匹配
        if (!Token::isValidateOperate($order->user_id)) {
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }

        //3.订单是否已经被支付
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }
}