<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3
 * Time: 16:47
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code=404;
    public  $msg='请求的Banner不存在';
    public  $errorCode=40000;

}