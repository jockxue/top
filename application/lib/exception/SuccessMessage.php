<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/11
 * Time: 22:12
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code=201;
    public $msg='ok';
    public $errorCode=0;
}