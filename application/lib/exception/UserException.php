<?php
/**
 * Created by PhpStorm.
 * User: jock
 * Date: 2019/6/11
 * Time: 21:38
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code=404;
    public $msg='当前用户不存在';
    public $errorCode=60000;

}