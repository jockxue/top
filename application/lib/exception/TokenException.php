<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/10
 * Time: 11:59
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code=401;
    public $msg='Token已过期或无效Token';
    public $errorCode=10001;
}