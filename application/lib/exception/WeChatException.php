<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/9
 * Time: 16:11
 */

namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code=400;
    public $msg='微信服务器接口调用失败';
    public $errorCode=999;
}