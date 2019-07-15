<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12
 * Time: 17:46
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code=403;
    public $msg='权限不够';
    public $errorCode=10001;
}