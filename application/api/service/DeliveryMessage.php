<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/7/8
 * Time: 10:42
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\exception\OrderExcption;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID='AL-kIZqBqJjQClOPepVw8WxT-1TdZl4Ajhy0tn0iM7g';
    public function sendDeliveryMessage($order,$tplJumpPage='')
    {
        if (!$order){
            throw new OrderExcption();
        }
        $this->tplID=self::DELIVERY_MSG_ID;
        $this->formID=$order->prepay_id;
        $this->page=$tplJumpPage;
        $this->prepareMessageData($order);
        $this->emphasisKeyWord='Keyword2.DATA';
        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }

    private function prepareMessageData($order)
    {
        $dt = new \DateTime();
        $data = [
            'keyword1' => [
                'value' => '顺风速运',
            ],
            'keyword2' => [
                'value' => $order->snap_name,
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' => $order->order_no
            ],
            'keyword4' => [
                'value' => $dt->format("Y-m-d H:i")
            ]
        ];
        $this->data = $data;
    }

    private function getUserOpenID($uid){
        $user=User::get($uid);
        if(!$user){
            throw new UserException();
        }
        return $user->openid;
    }
}