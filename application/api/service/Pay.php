<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/16
 * Time: 16:40
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderExcption;
use app\lib\exception\TokenException;
use think\Config;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class Pay
{
    private $orderID;
    private $orderNO;
    function __construct($orderID)
    {
        if(!$orderID)
        {
            throw new Exception('订单号不允许为空');
        }
        $this->orderID=$orderID;
    }
    public function pay()
    {
        //订单号可能根本不存在
        //订单号确实存在，但是订单号和当前用户是不匹配
        //订单有可能已经被支付

        $this->checkOrderValid(); //进行库存量的检测
        $orderService=new OrderService();
        $status=$orderService->checkOrderStock($this->orderID);
        if ($status['pass'])
        {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }
    private function makeWxPreOrder($totalPrice)
    {
        $openid=Token::getCurrentTokenVar('openid');
        if(!$openid)
        {
            throw new TokenException();
        }
        $wxOrderData=new \WxPayUnifiedOrder();

        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('钻工厂');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }
    private function getPaySignature($wxOrderData)
    {
        $wxOrder=\WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code']!='SUCCESS'||
            $wxOrder['result_code']!='SUCCESS')
        {
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        $this->recordPreOrder($wxOrder);
        $signature=$this->sign($wxOrder);
        return $signature;
    }
    private function sign($wxOrder)
    {
        $jsApiPayData=new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand=md5(time().mt_rand(0,100));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign=$jsApiPayData->MakeSign();
        $rawValues=$jsApiPayData->GetValues();
        $rawValues['paySign']=$sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id','=',$this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }
    private function checkOrderValid()
    {
        $order=OrderModel::where('id','=',$this->orderID)
            ->find();
        if(!$order)
        {
            throw new OrderExcption();
        }
        if (!Token::isValidOperate($order->user_id))
        {
            throw new TokenException([
                'msg'=>'订单与用户不匹配',
                'errorCode'=>10003
            ]);
        }
        if ($order->status !=OrderStatusEnum::UNPAID)
        {
            throw new OrderExcption([
                'msg'=>'订单已经付过啦',
                'errorCode'=>80003,
                'code'=>400
            ]);
        }
        $this->orderNO=$order->order_no;
        return true;
    }
}