<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 16:47
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\Order as OrderModel;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderExcption;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    protected $oProducts;//订单商品列表，也就是客户端传递过来的products参数
    protected $products;//真实的商品信息（包含库存量）
    protected $uid;//用户的id
    public function place($uid,$oProducts)
    {
        //oProducts和products作对比
        //products从数据库中查询出来
        $this->oProducts=$oProducts;
        $this->products=$this->getProductsByOrder($oProducts);
        $this->uid=$uid;
        $status=$this->getOrderStatus();
        if(!$status['pass'])
        {
           $status['order_id']=-1;
           return $status;
        }
        $orderSnap = $this->snapOrder($status);//开始创建订单
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;

    }

    private function createOrder($snap)
    {
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();//写入数据库

            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p)
            {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }
    }
    public static function makeOrderNo()//生成订单号
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    private function snapOrder($status) //生成订单快照
    {
        $snap=[
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatus'=>[],
            'snapAddress'=>null,
            'snapName'=>'',
            'snapImg'=>''
        ];
        $snap['orderPrice']=$status['orderPrice'];
        $snap['totalCount']=$status['totalCount'];
        $snap['pStatus']=$status['pStatusArray'];
        $snap['snapAddress']=json_encode($this->getUserAddress());
        $snap['snapName']=$this->products[0]['name'];
        $snap['snapImg']=$this->products[0]['main_img_url'];

        if (count($this->products)>1)
        {
            $snap['snapName'].='等';
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress=UserAddress::where('user_id','=',$this->uid)
            ->find();
        if (!$userAddress){
            throw new UserException([
                'msg'=>'用户收货地址不存在，下单失败',
                'errorCode'=>60001
            ]);
        }
        return  $userAddress->toArray();
    }
    public function checkOrderStock($orderID)//通过orderID拿到订单库存量检测
    {
        $oProducts=OrderProduct::where('order_id','=',$orderID)
            ->select();
        $this->oProducts=$oProducts;
        $this->products=$this->getProductsByOrder($oProducts);
        $status=$this->getOrderStatus();
        return $status;
    }

    private function getOrderStatus()//库存量检查
    {
        $status=[
            'pass'=>true,
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatusArray'=>[]
        ];
        foreach ($this->oProducts as $oProduct)
        {
            $pStatus=$this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'],$this->products
            );
            if(!$pStatus['haveStock']){
                $status['pass']=false;
            }
            $status['orderPrice']+=$pStatus['totalPrice'];
            $status['totalCount']+=$pStatus['counts'];
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }
    private function getProductStatus($oPID,$oCount,$products){
        $pIndex=-1;
        $pStatus=[
            'id'=>null,
            'haveStock'=>false,
            'counts'=>0,
            'price'=>0,
            'name'=>'',
            'totalPrice'=>0,
            'main_img_url'=>null
        ];
        for ($i=0;$i<count($products);$i++){
            if ($oPID==$products[$i]['id']){
                $pIndex=$i;
            }
        }
        if($pIndex==-1){
            //客户端传递的product_id有可能根本不存在
            throw new OrderExcption([
                'msg'=>'id为'.$oPID.'商品不存在，提交订单失败'
            ]);
        }
        else{
            $product=$products[$pIndex];
            $pStatus['id']=$product['id'];
            $pStatus['name']=$product['name'];
            $pStatus['counts']=$oCount;
            $pStatus['price']=$product['price'];
            $pStatus['main_img_url']=$product['main_img_url'];
            $pStatus['totalPrice']=$product['price']*$oCount;
            if($product['stock']-$oCount>=0){
                $pStatus['haveStock']=true;
            }
        }
        return $pStatus;
    }
    //根据订单信息查找真实的商品信息
    private function getProductsByOrder($oProducts)
    {
        $oPIDs=[];
        foreach ($oProducts as $item){
            array_push($oPIDs,$item['product_id']);
        }
        $products=Product::all($oPIDs)
            ->visible(['id','price','stock','name','main_img_url'])
            ->toArray();
        return $products;
    }

    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderExcption();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderExcption([
                'msg' => '未付款或者你已经更新过订单了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }

}