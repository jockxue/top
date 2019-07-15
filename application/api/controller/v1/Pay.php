<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/16
 * Time: 15:46
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList=[
        'checkExclusiveScope'=>['only'=>'getPreOrder']
    ];
    public function getPreOrder($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay=new PayService($id);
        return $pay->pay();
    }
    public function redirectNotify()
    {
        $notify=new WxNotify();
        $notify->Handle();

    }
    public function receiveNotify()
    {
        //检查库存量，超卖
        //更新这个订单的status状态
        //减库存
        //如果成功处理，我们返回微信成功处理的信息，否则，我们需要返回没有成功处理
        //特点：post：xml格式；不会携带参数
        $xmlData=file_get_contents('php://input');
        $result=curl_post_raw('http:/jock.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=17406',
            $xmlData);

    }
}