<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/16
 * Time: 17:36
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    const UNPAID=1;//待支付
    const PAID=2;//已支付
    const DELIVERED=3;//已发货
    const PAID_BUT_OUT_OF=4;//已支付但是库存不足
}