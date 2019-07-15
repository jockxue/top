<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/14
 * Time: 10:47
 */

namespace app\lib\exception;


class OrderExcption extends BaseException
{
    public $code=404;
    public $msg='订单不存在，请检查ID';
    public $errorCode=80000;
}