<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 14:01
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderExcption;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{
    //用户在选择商品后，向api提交包含他所选择商品的信息
    // API在接收到信息后，需要检査订单相关商品的库存量
    //有库存，把订单数据存入数据库中=下单成功了，返回客户端消息，告诉客户端可以支付了
    //调用我们的支付接口，进行支付
    //还需要再次进行库存量检测
    //服务器这边就可以调用微信的支付接口进行支付
    //小程序根据服务器返回结果拉起微信支付
    //微信会返回给我们一个支付的结果（异步）
    //成功：也需要进行库存量的检査
    //成功：进行库存量的扣除，失败：返回一个支付失败的结果

    protected $beforeActionList=[
        'checkExclusiveScope'=>['only'=>'placeOrder'],
        'checkPrimaryScope'=>['only'=>'getDetail,getSummaryByUser']
        //'checkSuperScope' => ['only' => 'delivery,getSummary']
    ];
    public function getSummaryByUser($page=1,$size=15)//我的订单分页
    {
        (new PagingParameter())->goCheck();
        $uid=TokenService::getCurrentUid();
        $pagingOrders=OrderModel::getSummaryByUser($uid,$page,$size);
        if ($pagingOrders->isEmpty()){
            return[
                'data'=>[],
                'current_page'=>$pagingOrders->getCurrentPage()
            ];
        }
        $data=$pagingOrders->hidden(['snap_item','snap_address','prepay_id'])  ->toArray();
        return[
            'data'=>$data,
            'current_page'=>$pagingOrders->getCurrentPage()
        ];
    }

    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1, $size = 5){
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail=OrderModel::get($id);
        if (!$orderDetail)
        {
            throw new OrderExcption();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $products=input('post.products/a');
        $uid=TokenService::getCurrentUid();

        $order=new OrderService();
        $status=$order->place($uid,$products);
        return $status;
    }

    public function delivery($id){
        (new IDMustBePositiveInt())->goCheck();
        $order=new OrderService();
        $success=$order->delivery($id);
        if ($success){
            return new SuccessMessage();
        }
    }
}