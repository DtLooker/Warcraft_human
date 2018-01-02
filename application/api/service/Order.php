<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2017/12/29
 * Time: 23:20
 */

namespace app\api\service;


use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;

class Order
{
    //客户端传递过来的订单信息
    private $oProducts;
    //真是商品信息，数据库内的商品信息
    private $products;
    private $uid;

    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;

        //获取订单状态
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            //如果订单状态不通过，增加一个order_id字段，并赋值为-1
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        //订单快照，即历史订单页面（因为商品可能下架，或者价格什么变化。所以需要保存下单时候的状态。而非动态获取）
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;

        return $order;
    }

    /**
     * 创建订单  多对多关系，换个角度，一个订单对多种商品。即一对多
     *
     *
     * @param $snap
     * @return array
     * @throws Exception
     */
    private function createOrder($snap)
    {
        //事物，要保证$order->save()与$orderProduct->saveAll()同时执行
        Db::startTrans();
        try {
            //先存储 1
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;

            //给oProduct 增加一个字段， PS： 注意 &$oProduct 写法
            foreach ($this->oProducts as &$oProduct){
                $oProduct['order_id'] = $orderID;
            }

            //再处理多
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            Db::commit();

            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];

        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

        // dechex() 把十进制转换为十六进制
        $orderSn = $yCode[intval(date('Y') - 2017)] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));

        return $orderSn;
    }

    //创建订单快照
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        //取第一个商品名和图片作为订单快照简介
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        //商品总类大于1，简介要加个‘等’字
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)
            ->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败',
                'errorCode' => 60001
            ]);
        }
        //模型返回的是对象，所以要转换成数组（默认数组,手动修改了）
        return $userAddress->toArray();
    }

    //根据订单信息查找对应商品的库存信息
    private function getProductsByOrder($oProducts)
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

        $oPIDs = [];
        foreach ($oProducts as $oProduct) {
            array_push($oPIDs, $oProduct['product_id']);
        }
        //用for循环获取所有商品id，放入数组中，统一查询，减少一次次查询数据库带来的数据库压力
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
    }

    /**
     * 获取整个订单的状态
     *
     * @return array
     */
    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'], $this->products
            );
            //订单中只要其中任意一个商品订单状态为false，整个订单状态为false
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['price'];
            $status['totalCount'] += $pStatus['counts'];

            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }

    /**
     * 获取单个商品的状态
     *
     * @param $oPID         订单中单个商品的id
     * @param $oCount       订单中单个商品购买的数量
     * @param $products     根据订单商品id 查询出来的对应的 数据库内的商品数量
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;

        //单个商品的状态
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'counts' => 0,
            'price' => 0,
            'name' => '',
            'totalPrice' => 0,
            'main_img_url' => null
        ];

        for ($i = 0; $i < count($products); $i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            //客户端传递过来的 product_id 可能不存在
            throw new OrderException([
                'msg' => 'id为' . $oPID . '的商品不存在，创建订单失败'
            ]);

        } else {

            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $oCount;
            $pStatus['price'] = $product['price'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;

            //只有一个商品的库存量多余订单数量，则为true
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }

        }
        return $pStatus;
    }
}