<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/3
 * Time: 22:41
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code=400;
    public $msg='参数错误';
    public $errorCode=10000;

}